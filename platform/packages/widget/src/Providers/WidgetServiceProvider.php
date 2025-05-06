<?php

namespace Botble\Widget\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Supports\DashboardMenuItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Theme\Events\RenderingAdminBar;
use Botble\Theme\Facades\AdminBar;
use Botble\Widget\Facades\WidgetGroup;
use Botble\Widget\Factories\WidgetFactory;
use Botble\Widget\Models\Widget;
use Botble\Widget\Repositories\Eloquent\WidgetRepository;
use Botble\Widget\Repositories\Interfaces\WidgetInterface;
use Botble\Widget\WidgetGroupCollection;
use Botble\Widget\Widgets\CoreSimpleMenu;
use Botble\Widget\Widgets\Text;
use Illuminate\Contracts\Foundation\Application;

class WidgetServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(WidgetInterface::class, function () {
            return new WidgetRepository(new Widget());
        });

        $this->app->bind('botble.widget', function (Application $app) {
            return new WidgetFactory($app);
        });

        $this->app->singleton('botble.widget-group-collection', function (Application $app) {
            return new WidgetGroupCollection($app);
        });
    }

    public function boot(): void
    {
        $this
            ->setNamespace('packages/widget')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadHelpers()
            ->loadRoutes()
            ->loadMigrations()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->publishAssets();

        $this->app->booted(function (): void {
            WidgetGroup::setGroup([
                'id' => 'primary_sidebar',
                'name' => trans('packages/widget::widget.primary_sidebar_name'),
                'description' => trans('packages/widget::widget.primary_sidebar_description'),
            ]);

            register_widget(CoreSimpleMenu::class);
            register_widget(Text::class);
        });

        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-core-widget')
                        ->parentId('cms-core-appearance')
                        ->priority(3)
                        ->name('packages/widget::widget.name')
                        ->icon('ti ti-layout')
                        ->route('widgets.index')
                        ->permissions('widgets.index')
                );
        });

        $this->app['events']->listen(RenderingAdminBar::class, function (): void {
            AdminBar::registerLink(
                trans('packages/widget::widget.name'),
                route('widgets.index'),
                'appearance',
                'widgets.index'
            );
        });
    }
}
