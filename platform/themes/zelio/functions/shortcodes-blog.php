<?php

use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\UiSelectorFieldOption;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\UiSelectorField;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Shortcode\Forms\ShortcodeForm;
use Botble\Theme\Facades\Theme;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

app()->booted(function () {
    if (! is_plugin_active('blog')) {
        return;
    }

    Event::listen(RouteMatched::class, function (): void {
        Shortcode::modifyAdminConfig('blog-posts', function (ShortcodeForm $form) {
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
                                        'image' => Theme::asset()->url("images/shortcodes/blog-posts/style-$i.png"),
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
                    'action_text',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Action text'))
                        ->collapsible('style', 1, Arr::get($form->getModel(), 'style', 1))
                )
                ->add(
                    'action_link',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Action link'))
                        ->collapsible('style', 1, Arr::get($form->getModel(), 'style', 1))
                )
                ->add(
                    'number_posts_per_row',
                    NumberField::class,
                    NumberFieldOption::make()
                        ->label(__('Number posts per row'))
                        ->min(1)
                        ->max(4)
                        ->defaultValue(3)
                );
        });
    });
});
