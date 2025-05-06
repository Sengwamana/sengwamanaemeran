<?php

namespace Botble\Captcha\Forms;

use Botble\Base\Facades\Html;
use Botble\Base\Forms\FieldOptions\AlertFieldOption;
use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\Fields\AlertField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Captcha\Facades\Captcha;
use Botble\Captcha\Http\Requests\Settings\CaptchaSettingRequest;
use Botble\Setting\Forms\SettingForm;

class CaptchaSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/captcha::captcha.settings.title'))
            ->setSectionDescription(trans('plugins/captcha::captcha.settings.description'))
            ->setValidatorClass(CaptchaSettingRequest::class)
            ->add(
                'enable_captcha',
                OnOffCheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('plugins/captcha::captcha.settings.enable_recaptcha'))
                    ->value($value = old('enable_captcha', Captcha::reCaptchaEnabled()))
            )
            ->addOpenCollapsible('enable_captcha', '1', $value)
            ->add(
                'captcha_setting_warning',
                AlertField::class,
                AlertFieldOption::make()
                    ->content(trans('plugins/captcha::captcha.settings.recaptcha_warning'))
                    ->type('warning')
            )
            ->add(
                'captcha_type',
                RadioField::class,
                RadioFieldOption::make()
                    ->label(trans('plugins/captcha::captcha.settings.type'))
                    ->choices([
                        'v2' => trans('plugins/captcha::captcha.settings.v2_description'),
                        'v3' => trans('plugins/captcha::captcha.settings.v3_description'),
                    ])
                    ->selected($captchaType = Captcha::reCaptchaType())
            )
            ->addOpenCollapsible('captcha_type', 'v3', $captchaType)
            ->add('captcha_hide_badge', 'onOffCheckbox', [
                'label' => trans('plugins/captcha::captcha.settings.hide_badge'),
                'value' => setting('captcha_hide_badge'),
            ])
            ->add('captcha_show_disclaimer', 'onOffCheckbox', [
                'label' => trans('plugins/captcha::captcha.settings.show_disclaimer'),
                'value' => setting('captcha_show_disclaimer', false),
            ])
            ->add('recaptcha_score', SelectField::class, [
                'label' => trans('plugins/captcha::captcha.settings.recaptcha_score'),
                'choices' => Captcha::scores(),
                'selected' => setting('recaptcha_score', 0.6),
            ])
            ->addCloseCollapsible('captcha_type', 'v3')
            ->add('captcha_site_key', TextField::class, [
                'label' => trans('plugins/captcha::captcha.settings.recaptcha_site_key'),
                'value' => setting('captcha_site_key'),
                'attr' => [
                    'placeholder' => trans('plugins/captcha::captcha.settings.recaptcha_site_key'),
                    'data-counter' => 120,
                ],
            ])
            ->add('captcha_secret', TextField::class, [
                'label' => trans('plugins/captcha::captcha.settings.recaptcha_secret'),
                'value' => setting('captcha_secret'),
                'attr' => [
                    'placeholder' => trans('plugins/captcha::captcha.settings.recaptcha_secret'),
                    'data-counter' => 120,
                ],
                'help_block' => [
                    'tag' => 'span',
                    'text' => trans(
                        'plugins/captcha::captcha.settings.recaptcha_credential_helper',
                        [
                            'link' => Html::link(
                                'https://www.google.com/recaptcha/admin#list',
                                trans('plugins/captcha::captcha.settings.recaptcha_credential_helper_here'),
                                ['target' => '_blank']
                            ),
                        ]
                    ),
                ],
            ])
            ->addSelectFormFields('enable_recaptcha')
            ->addCloseCollapsible('enable_captcha', '1')
            ->add(
                'enable_math_captcha',
                OnOffCheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('plugins/captcha::captcha.settings.enable_math_captcha'))
                    ->value($value = old('enable_math_captcha', Captcha::mathCaptchaEnabled()))
            )
            ->addOpenCollapsible('enable_math_captcha', '1', $value)
            ->addSelectFormFields('enable_math_captcha')
            ->addCloseCollapsible('enable_math_captcha', '1');
    }

    public function addSelectFormFields(string $key): static
    {
        foreach (Captcha::getFormsSupport() as $form => $title) {
            $this->add(
                Captcha::formSettingKey($form, $key),
                OnOffCheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('plugins/captcha::captcha.settings.enable_for_form', ['form' => $title]))
                    ->value(Captcha::formSetting($form, $key))
            );
        }

        return $this;
    }
}
