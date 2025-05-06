<?php

namespace ArchiElite\Announcement\Forms\Settings;

use ArchiElite\Announcement\AnnouncementHelper;
use ArchiElite\Announcement\Enums\AnnouncePlacement;
use ArchiElite\Announcement\Enums\FontSizeUnit;
use ArchiElite\Announcement\Enums\TextAlignment;
use ArchiElite\Announcement\Enums\WidthUnit;
use ArchiElite\Announcement\Http\Requests\Settings\AnnouncementSettingRequest;
use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Setting\Forms\SettingForm;

class AnnouncementSettingForm extends SettingForm
{
    public function buildForm(): void
    {
        parent::buildForm();

        $this
            ->setSectionTitle(trans('plugins/announcement::announcements.settings.name'))
            ->setSectionDescription(trans('plugins/announcement::announcements.settings.description'))
            ->setValidatorClass(AnnouncementSettingRequest::class)
            ->columns(4)
            ->add('announcement_placement', 'select', [
                'required' => true,
                'label' => trans('plugins/announcement::announcements.placement'),
                'choices' => AnnouncePlacement::labels(),
                'selected' => old('announcement_placement', setting('announcement_placement', AnnouncePlacement::TOP)),
                'colspan' => 2,
            ])
            ->add('announcement_text_alignment', 'select', [
                'required' => true,
                'label' => trans('plugins/announcement::announcements.text_alignment'),
                'choices' => TextAlignment::labels(),
                'selected' => old('announcement_text_alignment', setting('announcement_text_alignment', TextAlignment::CENTER)),
                'colspan' => 2,
            ])
            ->add('announcement_max_width', 'number', [
                'required' => true,
                'label' => trans('plugins/announcement::announcements.max_width'),
                'value' => old('announcement_max_width', setting('announcement_max_width', 1200)),
                'help_block' => [
                    'text' => trans('plugins/announcement::announcements.max_width_help'),
                ],
                'colspan' => 3,
            ])
            ->add('announcement_max_width_unit', 'select', [
                'required' => true,
                'label' => trans('plugins/announcement::announcements.max_width_unit'),
                'choices' => WidthUnit::labels(),
                'selected' => old('announcement_max_width_unit', setting('announcement_max_width_unit', 'px')),
            ])
            ->add('announcement_font_size', 'number', [
                'required' => true,
                'label' => trans('plugins/announcement::announcements.font_size'),
                'value' => old('announcement_font_size', setting('announcement_font_size', 0.9)),
                'help_block' => [
                    'text' => trans('plugins/announcement::announcements.font_size_help'),
                ],
                'colspan' => 3,
            ])
            ->add('announcement_font_size_unit', 'select', [
                'required' => true,
                'label' => trans('plugins/announcement::announcements.font_size_unit'),
                'choices' => FontSizeUnit::labels(),
                'selected' => old('announcement_font_size_unit', setting('announcement_font_size_unit', 'rem')),
            ])
            ->add('announcement_background_color', 'customColor', [
                'label' => trans('plugins/announcement::announcements.background_color'),
                'value' => old('announcement_background_color', setting('announcement_background_color', theme_option('primary_color', '#000'))),
                'colspan' => 2,
            ])
            ->add('announcement_text_color', 'customColor', [
                'label' => trans('plugins/announcement::announcements.text_color'),
                'value' => old('announcement_text_color', setting('announcement_text_color', '#fff')),
                'colspan' => 2,
            ])
            ->add('announcement_dismissible', 'onOffCheckbox', [
                'label' => trans('plugins/announcement::announcements.dismissible_label'),
                'value' => old('announcement_dismissible', setting('announcement_dismissible', false)),
                'colspan' => 4,
            ])
            ->add('announcement_autoplay', 'onOffCheckbox', [
                'label' => trans('plugins/announcement::announcements.autoplay_label'),
                'value' => old('announcement_autoplay', setting('announcement_autoplay', false)),
                'colspan' => 4,
                'attr' => [
                    'data-bb-toggle' => 'collapse',
                    'data-bb-target' => '.announcement-autoplay',
                ],
            ])
            ->add('announcement_autoplay_delay', 'number', [
                'label' => trans('plugins/announcement::announcements.autoplay_delay_label'),
                'value' => old('announcement_autoplay_delay', setting('announcement_autoplay_delay', 5000)),
                'help_block' => [
                    'text' => trans('plugins/announcement::announcements.autoplay_delay_help'),
                ],
                'wrapper' => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ' announcement-autoplay',
                    'style' => setting('announcement_autoplay', false) ? '' : 'display: none;',
                ],
                'colspan' => 4,
            ])
            ->add(
                'announcement_lazy_loading',
                OnOffCheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('plugins/announcement::announcements.lazy_loading'))
                    ->value(AnnouncementHelper::isLazyLoadingEnabled())
                    ->helperText(trans('plugins/announcement::announcements.lazy_loading_description'))
                    ->colspan(4)
            )
            ->add(
                'announcement_hide_on_mobile',
                OnOffCheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('plugins/announcement::announcements.hide_on_mobile'))
                    ->value(AnnouncementHelper::isHideOnMobile())
                    ->colspan(4)
            );
    }
}
