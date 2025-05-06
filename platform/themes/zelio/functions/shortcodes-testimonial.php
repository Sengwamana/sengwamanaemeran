<?php

use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\UiSelectorFieldOption;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\UiSelectorField;
use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Shortcode\Forms\ShortcodeForm;
use Botble\Shortcode\ShortcodeField;
use Botble\Testimonial\Models\Testimonial;
use Botble\Theme\Facades\Theme;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

app()->booted(function () {
    if (! is_plugin_active('testimonial')) {
        return;
    }

    Event::listen(RouteMatched::class, function (): void {
        Shortcode::register('testimonials', __('Testimonials'), __('Testimonials'), function (ShortcodeCompiler $shortcode) {
            if (! $testimonialIds = Shortcode::fields()->getIds('testimonial_ids', $shortcode)) {
                return null;
            }

            $testimonials = Testimonial::query()
                ->wherePublished()
                ->whereIn('id', $testimonialIds)
                ->get();

            if ($testimonials->isEmpty()) {
                return null;
            }

            return Theme::partial('shortcodes.testimonials.index', compact('shortcode', 'testimonials'));
        })
            ->setAdminConfig('testimonials', function (array $attributes) {
                return ShortcodeForm::createFromArray($attributes)
                    ->withLazyLoading()
                    ->add(
                        'style',
                        UiSelectorField::class,
                        UiSelectorFieldOption::make()
                            ->label(__('Style'))
                            ->emptyValue(1)
                            ->numberItemsPerRow(2)
                            ->choices(
                                collect(range(1, 2))->mapWithKeys(function ($i) {
                                    return [
                                        $i => [
                                            'label' => __('Style :i', ['i' => $i]),
                                            'image' => Theme::asset()->url("images/shortcodes/testimonials/style-$i.png"),
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
                        TextareaFieldOption::make()
                            ->label(__('Subtitle'))
                            ->rows(2)
                            ->collapsible('style', 1, Arr::get($attributes, 'style', 1))
                    )
                    ->add(
                        'testimonial_ids',
                        SelectField::class,
                        SelectFieldOption::make()
                            ->label(__('Testimonials'))
                            ->choices(
                                Testimonial::query()
                                    ->wherePublished()
                                    ->select(['id', 'name', 'company'])
                                    ->get()
                                    ->mapWithKeys(fn (Testimonial $item) => [$item->getKey() => sprintf('%s - %s', $item->name, $item->company)]) // @phpstan-ignore-line
                                    ->all()
                            )
                            ->multiple()
                            ->searchable()
                            ->selected(ShortcodeField::parseIds(Arr::get($attributes, 'testimonial_ids')))
                    )
                    ->add(
                        'shape_image',
                        MediaImageField::class,
                        MediaImageFieldOption::make()
                            ->label(__('Shape Image'))
                            ->collapsible('style', 1, Arr::get($attributes, 'style', 1))
                            ->helperText(__('Display shape image on the right side of the testimonial section.'))
                    );
            });
    });
});
