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
use Botble\Portfolio\Models\Package;
use Botble\Portfolio\Models\Project;
use Botble\Portfolio\Models\Service;
use Botble\Portfolio\Models\ServiceCategory;
use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Shortcode\Forms\ShortcodeForm;
use Botble\Shortcode\ShortcodeField;
use Botble\Theme\Facades\Theme;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

app()->booted(function () {
    if (! is_plugin_active('portfolio')) {
        return;
    }

    Event::listen(RouteMatched::class, function (): void {
        Shortcode::register(
            'projects',
            __('Projects'),
            __('Showcase your personal portfolio items, professional work, or creative projects.'),
            function (ShortcodeCompiler $shortcode) {
                $projectIds = Shortcode::fields()->getIds('project_ids', $shortcode);
                $limit = (int) $shortcode->limit ?: null;

                $projects = Project::query()
                    ->when($projectIds, fn ($query) => $query->whereIn('id', $projectIds))
                    ->when($limit, fn ($query) => $query->limit($limit))
                    ->wherePublished()
                    ->with(['slugable', 'metadata'])
                    ->oldest('order')
                    ->latest()
                    ->get();

                if ($projects->isEmpty()) {
                    return null;
                }

                $categoryIds = [];

                $projects
                    ->reject(fn ($project) => empty($project->getMetaData('category_ids', true)))
                    ->each(function ($project) use (&$categoryIds): void {
                        $categoryIds = array_merge($categoryIds, $project->getMetaData('category_ids', true));
                    });

                $categoryIds = array_unique($categoryIds);

                $categories = ServiceCategory::query()
                    ->wherePublished()
                    ->whereIn('id', $categoryIds)
                    ->get();

                $projects->map(function ($project) use ($categories) {
                    /**
                     * @var Project $project
                     */
                    $project->categories = $categories->filter(function ($category) use ($project) {
                        if (empty($project->getMetaData('category_ids', true))) {
                            return false;
                        }

                        return in_array($category->id, $project->getMetaData('category_ids', true));
                    });

                    return $project;
                });

                return Theme::partial('shortcodes.projects.index', compact('shortcode', 'categories', 'projects'));
            }
        );

        Shortcode::setAdminConfig('projects', function (array $attributes) {
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
                            collect(range(1, 4))->mapWithKeys(function ($i) {
                                return [
                                    $i => [
                                        'label' => __('Style :i', ['i' => $i]),
                                        'image' => Theme::asset()->url("images/shortcodes/projects/style-$i.png"),
                                    ],
                                ];
                            })->all()
                        )
                )
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()->label(__('Title'))->colspan(2)
                )
                ->add(
                    'subtitle',
                    TextareaField::class,
                    TextareaFieldOption::make()->label(__('Subtitle'))->rows(2)->colspan(2)
                )
                ->add(
                    'project_ids',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Projects'))
                        ->choices(
                            Project::query()
                                ->wherePublished()
                                ->pluck('name', 'id')
                                ->all()
                        )
                        ->multiple()
                        ->searchable()
                        ->selected(ShortcodeField::parseIds(Arr::get($attributes, 'project_ids')))
                        ->helperText(__('Leave empty to show all projects'))
                        ->colspan(2)
                )
                ->add(
                    'limit',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Limit'))
                        ->helperText(__('How many projects you want to show? (Leave empty to show all)'))
                )
                ->add(
                    'action_text',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Action text'))
                        ->collapsible('style', 1, Arr::get($attributes, 'style', 1))
                )
                ->add(
                    'action_link',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Action link'))
                        ->collapsible('style', 1, Arr::get($attributes, 'style', 1))
                )
                ->add(
                    'background_image',
                    MediaImageField::class,
                    MediaImageFieldOption::make()
                        ->label(__('Background image'))
                        ->colspan(2)
                )
                ->add(
                    'bottom_action_text',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Bottom action text'))
                        ->collapsible('style', 1, Arr::get($attributes, 'style', 1))
                )
                ->add(
                    'bottom_action_link',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Bottom action link'))
                        ->collapsible('style', 1, Arr::get($attributes, 'style', 1))
                );
        });

        Shortcode::register(
            'services',
            __('Services'),
            __('Showcases a dynamic list of services offered by the author.'),
            function (ShortcodeCompiler $shortcode) {
                $serviceIds = Shortcode::fields()->getIds('service_ids', $shortcode);
                $limit = (int) $shortcode->limit ?: null;

                $services = Service::query()
                    ->when($serviceIds, fn ($query) => $query->whereIn('id', $serviceIds))
                    ->when($limit, fn ($query) => $query->limit($limit))
                    ->wherePublished()
                    ->with('slugable')
                    ->oldest('order')
                    ->latest()
                    ->get();

                if ($services->isEmpty()) {
                    return null;
                }

                return Theme::partial('shortcodes.services.index', compact('shortcode', 'services'));
            }
        );

        Shortcode::setAdminConfig('services', function (array $attributes) {
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
                            collect(range(1, 4))->mapWithKeys(function ($i) {
                                return [
                                    $i => [
                                        'label' => __('Style :i', ['i' => $i]),
                                        'image' => Theme::asset()->url("images/shortcodes/services/style-$i.png"),
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
                    'service_ids',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Services'))
                        ->helperText(__('Leave empty to show all services'))
                        ->choices(
                            Service::query()
                                ->wherePublished()
                                ->pluck('name', 'id')
                                ->all()
                        )
                        ->multiple()
                        ->searchable()
                        ->selected(ShortcodeField::parseIds(Arr::get($attributes, 'service_ids')))
                )
                ->add(
                    'limit',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Limit'))
                        ->helperText(__('How many services you want to show? (Leave empty to show all)'))
                )
                ->add(
                    'bottom_text',
                    TextareaField::class,
                    TextareaFieldOption::make()
                        ->label(__('Bottom text'))
                        ->collapsible('style', 2, Arr::get($attributes, 'style', 1))
                )
                ->add(
                    'show_image_on_hover',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Show image on hover service item'))
                        ->choices([
                            'yes' => __('Yes'),
                            'no' => __('No'),
                        ])
                        ->collapsible('style', 1, Arr::get($attributes, 'style', 1))
                );
        });

        Shortcode::register(
            'pricing-plans',
            __('Pricing Plans'),
            __('Showcases a dynamic list of pricing plans offered by the author.'),
            function (ShortcodeCompiler $shortcode) {
                if (! $packageIds = Shortcode::fields()->getIds('package_ids', $shortcode)) {
                    return null;
                }

                $packages = Package::query()
                    ->whereIn('id', $packageIds)
                    ->wherePublished()
                    ->oldest('order')
                    ->latest()
                    ->get();

                if ($packages->isEmpty()) {
                    return null;
                }

                return Theme::partial('shortcodes.pricing-plans.index', compact('shortcode', 'packages'));
            }
        );

        Shortcode::setAdminConfig('pricing-plans', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'package_ids',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Packages'))
                        ->choices(
                            Package::query()
                                ->wherePublished()
                                ->pluck('name', 'id')
                                ->all()
                        )
                        ->multiple()
                        ->searchable()
                        ->selected(ShortcodeField::parseIds(Arr::get($attributes, 'package_ids')))
                );
        });
    });
});
