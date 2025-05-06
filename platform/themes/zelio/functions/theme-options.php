<?php

use Botble\Theme\Events\RenderingThemeOptionSettings;
use Botble\Theme\Facades\Theme;
use Botble\Theme\ThemeOption\Fields\ColorField;
use Botble\Theme\ThemeOption\Fields\MediaImageField;
use Botble\Theme\ThemeOption\Fields\SelectField;
use Botble\Theme\ThemeOption\Fields\TextField;
use Botble\Theme\ThemeOption\Fields\UiSelectorField;
use Botble\Theme\ThemeOption\ThemeOptionSection;

app('events')->listen(RenderingThemeOptionSettings::class, function (): void {
    theme_option()
        ->setField(
            ColorField::make()
                ->sectionId('styles')
                ->name('primary_color')
                ->label(__('Primary color'))
                ->defaultValue('#6e4ef2')
        )
        ->setField(
            ColorField::make()
                ->sectionId('styles')
                ->name('gradient_color')
                ->label(__('Gradient color'))
                ->defaultValue('#8c71ff')
        )
        ->setSection(
            ThemeOptionSection::make('footer')
                ->title(__('Footer'))
                ->icon('ti ti-brush')
                ->priority(5)
                ->fields([
                    MediaImageField::make()
                        ->sectionId('opt-text-subsection-general')
                        ->name('footer_background')
                        ->label(__('Footer background')),
                ])
        )
        ->setField(
            TextField::make()
                ->sectionId('opt-text-subsection-logo')
                ->name('site_name')
                ->label(__('Site name'))
                ->helperText(__('The name displayed next to the logo.'))
        )
        ->setField(
            MediaImageField::make()
                ->sectionId('opt-text-subsection-logo')
                ->name('logo_dark')
                ->label(__('Logo dark'))
        )
        ->setSection(
            ThemeOptionSection::make('styles')
                ->title(__('Styles'))
                ->icon('ti ti-palette')
                ->priority(1)
                ->fields([
                    SelectField::make()
                        ->label(__('Default theme color mode'))
                        ->name('default_theme_color_mode')
                        ->defaultValue('dark')
                        ->options([
                            'dark' => __('Dark'),
                            'light' => __('Light'),
                        ]),
                    SelectField::make()
                        ->label(__('Hide theme mode switcher'))
                        ->name('hide_theme_mode_switcher')
                        ->defaultValue('no')
                        ->options([
                            'no' => __('No'),
                            'yes' => __('Yes'),
                        ]),
                    UiSelectorField::make()
                        ->label(__('Header'))
                        ->name('header_style')
                        ->withoutAspectRatio()
                        ->defaultValue(1)
                        ->options(
                            collect(range(1, 3))->mapWithKeys(function ($i) {
                                return [
                                    $i => [
                                        'label' => __('Style :i', ['i' => $i]),
                                        'image' => Theme::asset()->url("images/header/style-$i.png"),
                                    ],
                                ];
                            })->all()
                        ),
                    UiSelectorField::make()
                        ->label(__('Footer'))
                        ->name('footer_style')
                        ->withoutAspectRatio()
                        ->defaultValue(1)
                        ->options(
                            collect(range(1, 3))->mapWithKeys(function ($i) {
                                return [
                                    $i => [
                                        'label' => __('Style :i', ['i' => $i]),
                                        'image' => Theme::asset()->url("images/footer/style-$i.png"),
                                    ],
                                ];
                            })->all()
                        ),
                    UiSelectorField::make()
                        ->label(__('Preloader'))
                        ->name('preloader_style')
                        ->defaultValue(1)
                        ->options(
                            collect(range(1, 3))->mapWithKeys(function ($i) {
                                return [
                                    $i => [
                                        'label' => __('Style :i', ['i' => $i]),
                                        'image' => Theme::asset()->url("images/preloader/style-$i.gif"),
                                    ],
                                ];
                            })
                                ->prepend([
                                    'label' => __('Disabled Preloader'),
                                ])
                                ->all()
                        ),
                ])
        )
        ->setField(UiSelectorField::make()
            ->label(__('Post item'))
            ->sectionId('opt-text-subsection-blog')
            ->name('post_item_style')
            ->withoutAspectRatio()
            ->defaultValue(1)
            ->options(
                collect(range(1, 3))->mapWithKeys(function ($i) {
                    return [
                        $i => [
                            'label' => __('Style :i', ['i' => $i]),
                            'image' => Theme::asset()->url("images/post-item/style-$i.png"),
                        ],
                    ];
                })->all()
            ))
        ->setField(TextField::make()
            ->label(__('Post item per row'))
            ->sectionId('opt-text-subsection-blog')
            ->name('post_item_per_row')
            ->defaultValue(3)
            ->helperText(__('Number of post items per row on the blog page.')))
        ->setField(
            MediaImageField::make()
                ->sectionId('opt-text-subsection-page')
                ->name('404_page_image')
                ->label(__('404 page image'))
        );
});
