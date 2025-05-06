<?php

use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Faq\Models\Faq;
use Botble\Faq\Models\FaqCategory;
use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Shortcode\Forms\ShortcodeForm;
use Botble\Theme\Facades\Theme;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;

app()->booted(function () {
    if (! is_plugin_active('blog')) {
        return;
    }

    Event::listen(RouteMatched::class, function (): void {
        Shortcode::register('faqs', __('FAQs'), __('FAQs'), function (ShortcodeCompiler $shortcode) {
            if (empty($categoryIds = Shortcode::fields()->getIds('category_ids', $shortcode))) {
                return null;
            }

            $faqs = Faq::query()
                ->wherePublished()
                ->whereIn('category_id', $categoryIds)
                ->get();

            return Theme::partial('shortcodes.faqs.index', compact('shortcode', 'faqs'));
        });

        Shortcode::setAdminConfig('faqs', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Title'))
                )
                ->add(
                    'category_ids',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Categories'))
                        ->choices(FaqCategory::query()->pluck('name', 'id')->all())
                        ->searchable()
                        ->multiple()
                );
        });
    });
});
