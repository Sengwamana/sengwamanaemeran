<?php

namespace Botble\Theme\Forms\Settings;

use Botble\Base\Facades\Html;
use Botble\Base\Forms\FieldOptions\CodeEditorFieldOption;
use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\CodeEditorField;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Setting\Forms\SettingForm;
use Botble\Theme\Http\Requests\WebsiteTrackingSettingRequest;

class WebsiteTrackingSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $googleTagManagerCode = setting('google_tag_manager_code');
        $googleTagManagerId = setting('google_tag_manager_id', setting('google_analytics'));
        $defaultType = setting('google_tag_manager_type', $googleTagManagerCode ? 'code' : 'id');

        $this
            ->setSectionTitle(trans('packages/theme::theme.settings.website_tracking.title'))
            ->setSectionDescription(trans('packages/theme::theme.settings.website_tracking.description'))
            ->setValidatorClass(WebsiteTrackingSettingRequest::class)
            ->add(
                'google_tag_manager_type',
                RadioField::class,
                RadioFieldOption::make()
                    ->choices([
                        'id' => trans('packages/theme::theme.settings.website_tracking.google_tag_id'),
                        'code' => trans('packages/theme::theme.settings.website_tracking.google_tag_code'),
                    ])
                    ->selected($targetValue = old('google_tag_manager_type', $defaultType))
            )
            ->add(
                'google_tag_manager_id',
                TextField::class,
                TextFieldOption::make()
                    ->collapsible('google_tag_manager_type', 'id', $targetValue)
                    ->value($googleTagManagerId)
                    ->placeholder(trans('packages/theme::theme.settings.website_tracking.google_tag_id_placeholder'))
                    ->helperText(
                        Html::link('https://support.google.com/analytics/answer/9539598#find-G-ID', attributes: ['target' => '_blank'])
                    )
            )
            ->add(
                'google_tag_manager_code',
                CodeEditorField::class,
                CodeEditorFieldOption::make()
                     ->collapsible('google_tag_manager_type', 'code', $targetValue)
                     ->value($googleTagManagerCode)
                     ->mode('html')
                     ->helperText(
                         Html::link('https://developers.google.com/tag-platform/gtagjs/install', attributes: ['target' => '_blank'])
                     )
            );
    }
}
