<?php

namespace Botble\Contact\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Forms\MetaBox;
use Botble\Contact\Enums\CustomFieldType;
use Botble\Contact\Http\Requests\CustomFieldRequest;
use Botble\Contact\Models\CustomField;
use Botble\Language\Facades\Language;

class CustomFieldForm extends FormAbstract
{
    public function setup(): void
    {
        Assets::addScripts('jquery-ui')
            ->addScriptsDirectly('vendor/core/plugins/contact/js/custom-field.js');

        $this
            ->model(CustomField::class)
            ->formClass('custom-field-form')
            ->setValidatorClass(CustomFieldRequest::class)
            ->add(
                'type',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/contact::contact.custom_field.type'))
                    ->required()
                    ->choices(CustomFieldType::labels())
            )
            ->add(
                'name',
                TextField::class,
                NameFieldOption::make()
                    ->required()
            )
            ->add(
                'placeholder',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/contact::contact.custom_field.placeholder'))
                    ->placeholder(trans('plugins/contact::contact.custom_field.placeholder'))
                    ->maxLength(120)
            )
            ->add(
                'required',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/contact::contact.custom_field.required'))
            )
            ->add(
                'order',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/contact::contact.custom_field.order'))
                    ->required()
                    ->defaultValue(999)
            )
            ->when(is_plugin_active('language'), function (FormAbstract $form): void {
                $isDefaultLanguage = ! defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')
                    || ! request()->input('ref_lang')
                    || request()->input('ref_lang') === Language::getDefaultLocaleCode();
                $customField = $form->getModel();
                $options = $customField->options->sortBy('order');

                $form->addMetaBox(
                    MetaBox::make('contact-custom-field-options')
                        ->hasTable()
                        ->attributes([
                            'class' => 'custom-field-options-box',
                            'style' => sprintf(
                                'display: %s;',
                                in_array(old('type', $customField), [CustomFieldType::DROPDOWN, CustomFieldType::RADIO]) ? 'block' : 'none;'
                            ),
                        ])
                        ->title(trans('plugins/contact::contact.custom_field.options'))
                        ->content(view(
                            'plugins/contact::partials.custom-field-options',
                            compact('options', 'isDefaultLanguage')
                        ))
                        ->footerContent($isDefaultLanguage ? view(
                            'plugins/contact::partials.custom-field-options-footer',
                            compact('isDefaultLanguage')
                        ) : null)
                );
            });
    }
}
