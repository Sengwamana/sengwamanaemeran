<?php

namespace Botble\Analytics\Providers;

use Botble\Base\Facades\Assets;
use Botble\Base\Supports\ServiceProvider;
use Botble\Dashboard\Events\RenderingDashboardWidgets;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Botble\PluginManagement\Events\RenderingPluginListingPage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app['events']->listen(RenderingDashboardWidgets::class, function (): void {
            if (
                ! config('plugins.analytics.general.enabled_dashboard_widgets')
                || setting('analytics_dashboard_widgets', '0') != '1'
            ) {
                return;
            }

            add_action(DASHBOARD_ACTION_REGISTER_SCRIPTS, [$this, 'registerScripts'], 18);
            add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'addAnalyticsWidgets'], 18, 2);
        });

        $this->app['events']->listen(RenderingPluginListingPage::class, function (): void {
            add_filter('core_layout_before_content', [$this, 'showMissingLibraryWarning'], 99);
        });
    }

    public function registerScripts(): void
    {
        if (Auth::guard()->user()->hasAnyPermission([
            'analytics.general',
            'analytics.page',
            'analytics.browser',
            'analytics.referrer',
        ])) {
            Assets::addScripts(['raphael', 'morris'])
                ->addStyles(['morris'])
                ->addStylesDirectly([
                    'vendor/core/plugins/analytics/libraries/jvectormap/jquery-jvectormap-1.2.2.css',
                ])
                ->addScriptsDirectly([
                    'vendor/core/plugins/analytics/libraries/jvectormap/jquery-jvectormap-1.2.2.min.js',
                    'vendor/core/plugins/analytics/libraries/jvectormap/jquery-jvectormap-world-mill-en.js',
                    'vendor/core/plugins/analytics/js/analytics.js',
                ]);
        }
    }

    public function addAnalyticsWidgets(array $widgets, Collection $widgetSettings): array
    {
        $dashboardWidgetInstance = new DashboardWidgetInstance();

        $dashboardWidgetInstance
            ->setPermission('analytics.general')
            ->setKey('widget_analytics_general')
            ->setTitle(trans('plugins/analytics::analytics.widget_analytics_general'))
            ->setIcon('fas fa-chart-line')
            ->setColor('warning')
            ->setRoute(route('analytics.general'))
            ->setHasLoadCallback(true)
            ->setIsEqualHeight(false)
            ->setSettings(['show_predefined_ranges' => true])
            ->init($widgets, $widgetSettings);

        $dashboardWidgetInstance
            ->setPermission('analytics.page')
            ->setKey('widget_analytics_page')
            ->setTitle(trans('plugins/analytics::analytics.widget_analytics_page'))
            ->setIcon('ti ti-news')
            ->setColor('info')
            ->setRoute(route('analytics.page'))
            ->setColumn('col-md-6 col-sm-6')
            ->setSettings(['show_predefined_ranges' => true])
            ->init($widgets, $widgetSettings);

        $dashboardWidgetInstance
            ->setPermission('analytics.browser')
            ->setKey('widget_analytics_browser')
            ->setTitle(trans('plugins/analytics::analytics.widget_analytics_browser'))
            ->setIcon('fab fa-safari')
            ->setColor('purple')
            ->setRoute(route('analytics.browser'))
            ->setColumn('col-md-6 col-sm-6')
            ->setSettings(['show_predefined_ranges' => true])
            ->init($widgets, $widgetSettings);

        return $dashboardWidgetInstance
            ->setPermission('analytics.referrer')
            ->setKey('widget_analytics_referrer')
            ->setTitle(trans('plugins/analytics::analytics.widget_analytics_referrer'))
            ->setIcon('fas fa-user-friends')
            ->setColor('info')
            ->setRoute(route('analytics.referrer'))
            ->setColumn('col-md-6 col-sm-6')
            ->setSettings(['show_predefined_ranges' => true])
            ->init($widgets, $widgetSettings);
    }

    public function showMissingLibraryWarning(?string $html): ?string
    {
        if (! Route::is('plugins.index') || class_exists('Google\ApiCore\Call')) {
            return $html;
        }

        return $html . view('plugins/analytics::missing-library-warning')->render();
    }
}
