<?php

namespace Botble\Slug\Forms;

use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Setting\Forms\SettingForm;
use Botble\Slug\Facades\SlugHelper;
use Botble\Slug\Http\Requests\SlugSettingsRequest;
use Illuminate\Support\Str;

class SlugSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $form = $this
            ->setSectionTitle(trans('packages/slug::slug.settings.title'))
            ->setSectionDescription(trans('packages/slug::slug.settings.description'))
            ->setValidatorClass(SlugSettingsRequest::class);

        foreach (SlugHelper::supportedModels() as $model => $name) {
            $settingKey = SlugHelper::getPermalinkSettingKey($model);
            $settingValue = SlugHelper::getPrefix($model, '', false);

            $form
                ->add($settingKey, TextField::class, [
                    'label' => trans('packages/slug::slug.prefix_for', ['name' => $name]),
                    'value' => trim(old($settingKey, $settingValue), '/'),
                    'help_block' => [
                        'text' => SlugHelper::getHelperTextForPrefix($model),
                    ],
                ])
                ->add($settingKey . '-model-key', 'hidden', [
                    'value' => $model,
                ]);
        }

        $form
            ->add(SlugHelper::getSettingKey('public_single_ending_url'), TextField::class, [
                'label' => trans('packages/slug::slug.public_single_ending_url'),
                'value' => SlugHelper::getPublicSingleEndingURL(),
                'help_block' => [
                    'text' => SlugHelper::getHelperText(
                        Str::slug('your url here'),
                        SlugHelper::getPublicSingleEndingURL()
                    ),
                ],
            ])
            ->add(SlugHelper::getSettingKey('slug_turn_off_automatic_url_translation_into_latin'), OnOffField::class, [
                'label' => trans('packages/slug::slug.settings.turn_off_automatic_url_translation_into_latin'),
                'value' => SlugHelper::turnOffAutomaticUrlTranslationIntoLatin(),
            ])
            ->add('html', HtmlField::class, [
                'html' => apply_filters(
                    'setting_permalink_meta_boxes',
                    null,
                    request()->route()->parameters(),
                ),
            ]);
    }
}
