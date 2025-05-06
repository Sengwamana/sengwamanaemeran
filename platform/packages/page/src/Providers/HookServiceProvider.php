<?php

namespace Botble\Page\Providers;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\RepositoryHelper;
use Botble\Base\Supports\ServiceProvider;
use Botble\Dashboard\Events\RenderingDashboardWidgets;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Botble\Media\Facades\RvMedia;
use Botble\Menu\Events\RenderingMenuOptions;
use Botble\Menu\Facades\Menu;
use Botble\Page\Models\Page;
use Botble\Page\Services\PageService;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Slug\Models\Slug;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\NameColumn;
use Botble\Theme\Events\RenderingThemeOptionSettings;
use Botble\Theme\Facades\Theme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Menu::addMenuOptionModel(Page::class);

        $this->app['events']->listen(RenderingMenuOptions::class, function (): void {
            add_action(MENU_ACTION_SIDEBAR_OPTIONS, [$this, 'registerMenuOptions'], 10);
        });

        $this->app['events']->listen(RenderingDashboardWidgets::class, function (): void {
            add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'addPageStatsWidget'], 15, 2);
        });

        add_filter(BASE_FILTER_PUBLIC_SINGLE_DATA, [$this, 'handleSingleView'], 1);

        $this->app['events']->listen(RenderingThemeOptionSettings::class, function (): void {
            $pages = Page::query()
                ->wherePublished();

            $pages = RepositoryHelper::applyBeforeExecuteQuery($pages, new Page())
                ->pluck('name', 'id')
                ->all();

            theme_option()
                ->when($pages, function () use ($pages): void {
                    theme_option()
                        ->setSection([
                            'title' => trans('packages/page::pages.theme_options.title'),
                            'id' => 'opt-text-subsection-page',
                            'subsection' => true,
                            'icon' => 'ti ti-book',
                            'fields' => [
                                [
                                    'id' => 'homepage_id',
                                    'type' => 'customSelect',
                                    'label' => trans('packages/page::pages.theme_options.your_home_page_display'),
                                    'attributes' => [
                                        'name' => 'homepage_id',
                                        'list' => [0 => trans('core/base::forms.select_placeholder')] + $pages,
                                        'value' => '',
                                        'options' => [
                                            'class' => 'form-control',
                                        ],
                                    ],
                                ],
                            ],
                        ]);
                });
        });

        $this->app['events']->listen(RouteMatched::class, function (): void {
            if (defined('THEME_FRONT_HEADER')) {
                add_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, function ($screen, $page): void {
                    add_filter(THEME_FRONT_HEADER, function (?string $html) use ($page): string|null {
                        if (get_class($page) != Page::class) {
                            return $html;
                        }

                        $schema = [
                            '@context' => 'https://schema.org',
                            '@type' => 'Organization',
                            'name' => rescue(fn () => SeoHelper::openGraph()->getProperty('site_name')),
                            'url' => $page->url,
                            'logo' => [
                                '@type' => 'ImageObject',
                                'url' => RvMedia::getImageUrl(Theme::getLogo()),
                            ],
                        ];

                        return $html . Html::tag('script', json_encode($schema, JSON_UNESCAPED_UNICODE), ['type' => 'application/ld+json'])
                                ->toHtml();
                    }, 2);
                }, 2, 2);
            }

            add_filter(PAGE_FILTER_FRONT_PAGE_CONTENT, fn (?string $html) => (string) $html, 1, 2);

            add_filter('table_name_column_data', [$this, 'appendPageName'], 2, 3);
        });
    }

    public function appendPageName(string $value, Model $model, Column $column)
    {
        if ($column instanceof NameColumn && $model instanceof Page) {
            $value = apply_filters(PAGE_FILTER_PAGE_NAME_IN_ADMIN_LIST, $value, $model);
        }

        return $value;
    }

    public function registerMenuOptions(): void
    {
        if (Auth::guard()->user()->hasPermission('pages.index')) {
            Menu::registerMenuOptions(Page::class, trans('packages/page::pages.menu'));
        }
    }

    public function addPageStatsWidget(array $widgets, Collection $widgetSettings): array
    {
        $pages = Page::query()->wherePublished()->count();

        return (new DashboardWidgetInstance())
            ->setType('stats')
            ->setPermission('pages.index')
            ->setTitle(trans('packages/page::pages.pages'))
            ->setKey('widget_total_pages')
            ->setIcon('ti ti-files')
            ->setColor('yellow')
            ->setStatsTotal($pages)
            ->setRoute(route('pages.index'))
            ->setColumn('col-12 col-md-6 col-lg-3')
            ->init($widgets, $widgetSettings);
    }

    public function handleSingleView(Slug|array $slug): Slug|array
    {
        return (new PageService())->handleFrontRoutes($slug);
    }
}
