<?php

use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\UiSelectorFieldOption;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\UiSelectorField;
use Botble\Contact\Forms\ShortcodeContactAdminConfigForm;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Shortcode\Forms\FieldOptions\ShortcodeTabsFieldOption;
use Botble\Shortcode\Forms\Fields\ShortcodeTabsField;
use Botble\Theme\Facades\Theme;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;

app()->booted(function () {
    if (! is_plugin_active('contact')) {
        return;
    }

    Event::listen(RouteMatched::class, function (): void {
        add_filter(CONTACT_FORM_TEMPLATE_VIEW, function () {
            return Theme::getThemeNamespace('partials.shortcodes.contact-form.index');
        });

        Shortcode::modifyAdminConfig('contact-form', function (ShortcodeContactAdminConfigForm $form) {
            return $form
                ->add(
                    'style',
                    UiSelectorField::class,
                    UiSelectorFieldOption::make()
                        ->label(__('Style'))
                        ->emptyValue(1)
                        ->withoutAspectRatio()
                        ->choices(
                            collect(range(1, 3))->mapWithKeys(function ($i) {
                                return [
                                    $i => [
                                        'label' => __('Style :i', ['i' => $i]),
                                        'image' => Theme::asset()->url("images/shortcodes/contact/style-$i.png"),
                                    ],
                                ];
                            })->all()
                        )
                )
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()->label(__('Title'))
                )
                ->add(
                    'subtitle',
                    TextareaField::class,
                    TextareaFieldOption::make()->label(__('Subtitle'))->rows(2)
                )
                ->add(
                    'contact_info',
                    ShortcodeTabsField::class,
                    ShortcodeTabsFieldOption::make()
                        ->label(__('Contact Info'))
                        ->fields([
                            'label' => [
                                'type' => 'text',
                                'title' => __('Label'),
                            ],
                            'content' => [
                                'type' => 'text',
                                'title' => __('Content'),
                            ],
                            'icon' => [
                                'type' => 'coreIcon',
                                'title' => __('Icon'),
                            ],
                            'url' => [
                                'type' => 'text',
                                'title' => __('URL'),
                            ],
                        ])
                        ->attrs($form->getModel())
                );
        });
    });
});
