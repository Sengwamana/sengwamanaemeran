<?php

namespace Botble\Setting\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Setting\Http\Requests\GeneralSettingRequest;
use DateTimeZone;

class GeneralSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        Assets::usingVueJS()
            ->addStylesDirectly('vendor/core/core/setting/css/admin-email.css')
            ->addScriptsDirectly([
                'vendor/core/core/setting/js/admin-email.js',
                'vendor/core/core/setting/js/license-component.js',
            ]);

        $this
            ->setSectionTitle(trans('core/setting::setting.general.title'))
            ->setSectionDescription(trans('core/setting::setting.general.description'))
            ->contentOnly()
            ->setValidatorClass(GeneralSettingRequest::class)
            ->add(
                'admin_email',
                HtmlField::class,
                HtmlFieldOption::make()->view('core/setting::partials.admin-email-field')
            )
            ->add(
                'time_zone',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('core/setting::setting.general.time_zone'))
                    ->choices(array_combine(DateTimeZone::listIdentifiers(), DateTimeZone::listIdentifiers()))
                    ->selected(setting('time_zone', 'UTC'))
                    ->searchable()
            )
            ->add('enable_send_error_reporting_via_email', 'onOffCheckbox', [
                'label' => trans('core/setting::setting.general.enable_send_error_reporting_via_email'),
                'value' => setting('enable_send_error_reporting_via_email'),
            ])
            ->when(
                apply_filters(BASE_FILTER_AFTER_SETTING_CONTENT, null),
                function (GeneralSettingForm $form, $settingContent): void {
                    $form
                        ->add(
                            'html',
                            HtmlField::class,
                            HtmlFieldOption::make()
                                ->content('</div></div><div class="card mt-3 overflow-hidden"><div class="card-body">' . $settingContent)
                        );
                }
            );
    }
}
