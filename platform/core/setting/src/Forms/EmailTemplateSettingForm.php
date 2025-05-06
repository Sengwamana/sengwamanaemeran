<?php

namespace Botble\Setting\Forms;

use Botble\Base\Forms\FieldOptions\CodeEditorFieldOption;
use Botble\Base\Forms\FieldOptions\EmailFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\RepeaterFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\CodeEditorField;
use Botble\Base\Forms\Fields\EmailField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\RepeaterField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Setting\Http\Requests\EmailTemplateSettingRequest;

class EmailTemplateSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $fields = [
            [
                'type' => 'text',
                'label' => trans('core/setting::setting.email.social_links.name'),
                'attributes' => [
                    'name' => 'name',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'type' => 'text',
                'label' => trans('core/setting::setting.email.social_links.url'),
                'attributes' => [
                    'name' => 'url',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'type' => 'mediaImage',
                'label' => trans('core/setting::setting.email.social_links.icon_image'),
                'attributes' => [
                    'name' => 'image',
                    'value' => null,
                ],
            ],
        ];

        $this
            ->setUrl(route('settings.email.template.update-settings'))
            ->contentOnly()
            ->setSectionTitle(trans('core/setting::setting.email.email_template_settings'))
            ->setSectionDescription(trans('core/setting::setting.email.email_template_settings_description'))
            ->setValidatorClass(EmailTemplateSettingRequest::class)
            ->setFormOptions(['class' => 'mb-5'])
            ->add(
                'email_template_logo',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(trans('core/setting::setting.email.email_template_logo'))
                    ->value(apply_filters('email_template_logo', setting('email_template_logo')))
                    ->helperText(
                        apply_filters(
                            'email_template_logo_helper_text',
                            trans('core/setting::setting.email.email_template_logo_helper_text'),
                        )

                        . '<br>' . trans('core/setting::setting.email.image_upload_supported')
                    )
                    ->addAttribute('accept', '.png, .jpg, .jpeg, .gif')
                    ->addAttribute('allow_thumb', false)
            )
            ->add(
                'email_template_email_contact',
                EmailField::class,
                EmailFieldOption::make()
                    ->label(trans('core/setting::setting.email.email_template_email_contact'))
                    ->value(setting('email_template_email_contact'))
                    ->helperText(trans('core/setting::setting.email.email_template_email_contact_helper_text'))
            )
            ->add(
                'email_template_copyright_text',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.email.email_template_copyright_text'))
                    ->value(apply_filters('email_template_copyright_text', setting('email_template_copyright_text')))
                    ->helperText(apply_filters('email_template_copyright_helper_text', trans('core/setting::setting.email.email_template_copyright_text_helper_text')))
            )
            ->add(
                'email_template_max_height_for_logo',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(__('Logo height (px)'))
                    ->helperText(__('Set the height of the logo in pixels. The default value is :default.', ['default' => '40px']))
                    ->attributes([
                        'min' => 1,
                        'max' => 150,
                    ])
                    ->value(setting('email_template_max_height_for_logo', 40))
            )
            ->add(
                'email_template_custom_css',
                CodeEditorField::class,
                CodeEditorFieldOption::make()
                    ->label(trans('core/setting::setting.email.email_template_custom_css'))
                    ->value(setting('email_template_custom_css'))
                    ->mode('css')
            )
            ->add(
                'email_template_social_links',
                RepeaterField::class,
                RepeaterFieldOption::make()
                    ->label(trans('core/setting::setting.email.email_template_social_links'))
                    ->value(setting('email_template_social_links', []))
                    ->fields($fields)
            )
        ;
    }
}
