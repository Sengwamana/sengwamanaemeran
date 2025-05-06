<?php

namespace Botble\Captcha\Providers;

use Botble\ACL\Forms\Auth\ForgotPasswordForm;
use Botble\ACL\Forms\Auth\LoginForm;
use Botble\ACL\Forms\Auth\ResetPasswordForm;
use Botble\ACL\Http\Requests\ForgotPasswordRequest;
use Botble\ACL\Http\Requests\LoginRequest;
use Botble\ACL\Http\Requests\ResetPasswordRequest;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Captcha\Captcha;
use Botble\Captcha\CaptchaV3;
use Botble\Captcha\Facades\Captcha as CaptchaFacade;
use Botble\Captcha\Forms\Fields\MathCaptchaField;
use Botble\Captcha\Forms\Fields\ReCaptchaField;
use Botble\Captcha\MathCaptcha;
use Botble\Setting\PanelSections\SettingOthersPanelSection;
use Botble\Support\Http\Requests\Request;
use Botble\Theme\FormFront;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Http\Request as IlluminateRequest;
use Illuminate\Routing\Events\Routing;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class CaptchaServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    protected bool $defer = false;

    public function register(): void
    {
        $this->app->singleton('captcha', function () {
            if (setting('captcha_type') === 'v3') {
                return new CaptchaV3(setting('captcha_site_key'), setting('captcha_secret'));
            }

            return new Captcha(setting('captcha_site_key'), setting('captcha_secret'));
        });

        $this->app->singleton('math-captcha', function ($app) {
            return new MathCaptcha($app['session']);
        });

        AliasLoader::getInstance()->alias('Captcha', CaptchaFacade::class);
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/captcha')
            ->loadAndPublishConfigurations(['general', 'permissions'])
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations();

        $this->bootValidator();

        PanelSectionManager::default()->beforeRendering(function (): void {
            PanelSectionManager::registerItem(
                SettingOthersPanelSection::class,
                fn () => PanelSectionItem::make('captcha')
                    ->setTitle(trans('plugins/captcha::captcha.settings.title'))
                    ->withIcon('ti ti-refresh')
                    ->withPriority(150)
                    ->withDescription(trans('plugins/captcha::captcha.settings.panel_description'))
                    ->withRoute('captcha.settings')
            );
        });

        CaptchaFacade::registerFormSupport(LoginForm::class, LoginRequest::class, trans('plugins/captcha::captcha.admin_login_form'));
        CaptchaFacade::registerFormSupport(ForgotPasswordForm::class, ForgotPasswordRequest::class, trans('plugins/captcha::captcha.admin_forgot_password_form'));
        CaptchaFacade::registerFormSupport(ResetPasswordForm::class, ResetPasswordRequest::class, trans('plugins/captcha::captcha.admin_reset_password_form'));

        FormAbstract::beforeRendering(function (FormAbstract $form): void {
            if (! CaptchaFacade::isEnabled() && ! CaptchaFacade::mathCaptchaEnabled()) {
                return;
            }

            $fieldKey = 'submit';

            $attributes = [
                'colspan' => $form->getColumns('lg'),
            ];

            if ($form instanceof FormFront) {
                $fieldKey = $form->getFormEndKey() ?: ($form->has($fieldKey) ? $fieldKey : array_key_last($form->getFields()));

                if ($form->getFormInputWrapperClass()) {
                    $attributes['wrapper'] = ['class' => $form->getFormInputWrapperClass()];
                }

                if ($form->getFormLabelClass()) {
                    $attributes['label_attr'] = ['class' => $form->getFormLabelClass()];
                }

                if ($form->getFormInputClass()) {
                    $attributes['attr'] = ['class' => $form->getFormInputClass()];
                }
            }

            if (CaptchaFacade::reCaptchaEnabled() && ! $form->has('recaptcha') && CaptchaFacade::formSetting($form::class, 'enable_recaptcha')) {
                $form->addBefore(
                    $fieldKey,
                    'recaptcha',
                    ReCaptchaField::class,
                    $attributes
                );
            }

            if (CaptchaFacade::mathCaptchaEnabled() && ! $form->has('math_captcha') && CaptchaFacade::formSetting($form::class, 'enable_math_captcha')) {
                $form->addBefore(
                    $fieldKey,
                    'math_captcha',
                    MathCaptchaField::class,
                    $attributes
                );
            }
        });

        $this->app['events']->listen(Routing::class, function (): void {
            add_filter('core_request_rules', function (array $rules, Request $request) {
                if (! CaptchaFacade::isEnabled() && ! CaptchaFacade::mathCaptchaEnabled()) {
                    return $rules;
                }

                CaptchaFacade::getFormsSupport();

                $form = CaptchaFacade::formByRequest($request::class);

                if (! $form) {
                    return $rules;
                }

                if (CaptchaFacade::reCaptchaEnabled() && CaptchaFacade::formSetting($form, 'enable_recaptcha')) {
                    $rules = [...$rules, ...CaptchaFacade::rules()];
                }

                if (CaptchaFacade::mathCaptchaEnabled() && CaptchaFacade::formSetting($form, 'enable_math_captcha')) {
                    $rules = [...$rules, ...CaptchaFacade::mathCaptchaRules()];
                }

                return $rules;
            }, 128, 2);
        });

        add_filter('form_extra_fields_render', function (?string $fields = null, ?string $form = null): ?string {
            if (! CaptchaFacade::isEnabled() && ! CaptchaFacade::mathCaptchaEnabled()) {
                return $fields;
            }

            return $fields . view('plugins/captcha::forms.old-version-support', compact('form'))->render();
        }, 128, 2);

        add_action('form_extra_fields_validate', function (IlluminateRequest $request, ?string $form = null): void {
            if (! CaptchaFacade::isEnabled() && ! CaptchaFacade::mathCaptchaEnabled()) {
                return;
            }

            if (
                CaptchaFacade::reCaptchaEnabled()
                && (! $form || ! class_exists($form) || CaptchaFacade::formSetting($form, 'enable_recaptcha'))
                && ! $request instanceof Request
            ) {
                Validator::validate($request->input(), CaptchaFacade::rules());
            }

            if (CaptchaFacade::mathCaptchaEnabled() && (! $form || ! class_exists($form) || CaptchaFacade::formSetting($form, 'enable_math_captcha'))) {
                Validator::validate($request->input(), CaptchaFacade::mathCaptchaRules());
            }
        }, 999, 2);
    }

    public function bootValidator(): void
    {
        $app = $this->app;

        /**
         * @var Validator $validator
         */
        $validator = $app['validator'];
        $validator->extend('captcha', function ($attribute, $value, $parameters) use ($app) {
            if (! $app['captcha']->reCaptchaEnabled()) {
                return true;
            }

            if (! is_string($value)) {
                return false;
            }

            if (setting('captcha_type') === 'v3') {
                if (empty($parameters)) {
                    $parameters = ['form', (float) setting('recaptcha_score', 0.6)];
                }
            } else {
                $parameters = $this->mapParameterToOptions($parameters);
            }

            return $app['captcha']->verify($value, $this->app['request']->getClientIp(), $parameters);
        }, __('Captcha Verification Failed!'));

        $validator->extend('math_captcha', function ($attribute, $value) {
            if (! is_string($value)) {
                return false;
            }

            return $this->app['math-captcha']->verify($value);
        }, __('Math Captcha Verification Failed!'));
    }

    public function mapParameterToOptions(?array $parameters = []): array
    {
        if (! is_array($parameters)) {
            return [];
        }

        $options = [];

        foreach ($parameters as $parameter) {
            $option = explode(':', $parameter);
            if (count($option) === 2) {
                Arr::set($options, $option[0], $option[1]);
            }
        }

        return $options;
    }

    public function provides(): array
    {
        return ['captcha', 'math-captcha'];
    }
}
