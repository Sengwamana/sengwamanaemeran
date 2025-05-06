<?php

namespace Botble\Contact\Forms\Settings;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Contact\Http\Requests\Settings\ContactSettingRequest;
use Botble\Setting\Forms\SettingForm;

class ContactSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        Assets::addStylesDirectly('vendor/core/core/base/libraries/tagify/tagify.css')
            ->addScriptsDirectly([
                'vendor/core/core/base/libraries/tagify/tagify.js',
                'vendor/core/core/base/js/tags.js',
            ]);

        $this
            ->setSectionTitle(trans('plugins/contact::contact.settings.title'))
            ->setSectionDescription(trans('plugins/contact::contact.settings.description'))
            ->setValidatorClass(ContactSettingRequest::class)
            ->add(
                'blacklist_keywords',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->addAttribute('class', 'tags form-control')
                    ->addAttribute('data-counter', '250')
                    ->value(setting('blacklist_keywords'))
                    ->label(trans('plugins/contact::contact.settings.blacklist_keywords'))
                    ->placeholder(trans('plugins/contact::contact.settings.blacklist_keywords_placeholder'))
                    ->helperText(trans('plugins/contact::contact.settings.blacklist_keywords_helper'))
            )
            ->add(
                'receiver_emails',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->addAttribute('class', 'tags form-control')
                    ->value(setting('receiver_emails'))
                    ->label(trans('plugins/contact::contact.settings.receiver_emails'))
                    ->placeholder(trans('plugins/contact::contact.settings.receiver_emails_placeholder'))
                    ->helperText(trans('plugins/contact::contact.settings.receiver_emails_helper'))
            );
    }
}
