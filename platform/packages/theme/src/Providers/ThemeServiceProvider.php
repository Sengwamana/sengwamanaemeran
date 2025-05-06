<?php

namespace Botble\Theme\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\DashboardMenu as DashboardMenuSupport;
use Botble\Base\Supports\DashboardMenuItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Setting\PanelSections\SettingCommonPanelSection;
use Botble\Theme\Commands\ThemeActivateCommand;
use Botble\Theme\Commands\ThemeAssetsPublishCommand;
use Botble\Theme\Commands\ThemeAssetsRemoveCommand;
use Botble\Theme\Commands\ThemeOptionCheckMissingCommand;
use Botble\Theme\Commands\ThemeRemoveCommand;
use Botble\Theme\Commands\ThemeRenameCommand;
use Botble\Theme\Contracts\Theme as ThemeContract;
use Botble\Theme\Events\RenderingAdminBar;
use Botble\Theme\Theme;

class ThemeServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->singleton(ThemeContract::class, Theme::class);
    }

    public function boot(): void
    {
        $this
            ->setNamespace('packages/theme')
            ->loadAndPublishConfigurations(['general', 'permissions'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadHelpers()
            ->loadRoutes()
            ->publishAssets();

        DashboardMenu::default()->beforeRetrieving(function (DashboardMenuSupport $menu): void {
            $config = $this->app['config'];

            $menu
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-core-appearance')
                        ->priority(2000)
                        ->name('packages/theme::theme.appearance')
                        ->icon('ti ti-brush')
                )
                ->when(
                    $config->get('packages.theme.general.display_theme_manager_in_admin_panel', true),
                    function (DashboardMenuSupport $menu): void {
                        $menu->registerItem(
                            DashboardMenuItem::make()
                                ->id('cms-core-theme')
                                ->priority(1)
                                ->parentId('cms-core-appearance')
                                ->name('packages/theme::theme.name')
                                ->icon('ti ti-palette')
                                ->route('theme.index')
                                ->permissions('theme.index')
                        );
                    }
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-core-theme-option')
                        ->priority(4)
                        ->parentId('cms-core-appearance')
                        ->name('packages/theme::theme.theme_options')
                        ->icon('ti ti-list-tree')
                        ->route('theme.options')
                        ->permissions('theme.options')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-core-appearance-custom-css')
                        ->priority(5)
                        ->parentId('cms-core-appearance')
                        ->name('packages/theme::theme.custom_css')
                        ->icon('ti ti-file-type-css')
                        ->route('theme.custom-css')
                        ->permissions('theme.custom-css')
                )
                ->when(
                    $config->get('packages.theme.general.enable_custom_js'),
                    function (DashboardMenuSupport $menu): void {
                        $menu->registerItem(
                            DashboardMenuItem::make()
                                ->id('cms-core-appearance-custom-js')
                                ->priority(6)
                                ->parentId('cms-core-appearance')
                                ->name('packages/theme::theme.custom_js')
                                ->icon('ti ti-file-type-js')
                                ->route('theme.custom-js')
                                ->permissions('theme.custom-js')
                        );
                    }
                )
                ->when(
                    $config->get('packages.theme.general.enable_custom_html'),
                    function (DashboardMenuSupport $menu): void {
                        $menu->registerItem(
                            DashboardMenuItem::make()
                                ->id('cms-core-appearance-custom-html')
                                ->priority(6)
                                ->parentId('cms-core-appearance')
                                ->name('packages/theme::theme.custom_html')
                                ->icon('ti ti-file-type-html')
                                ->route('theme.custom-html')
                                ->permissions('theme.custom-html')
                        );
                    }
                )
                ->when(
                    $config->get('packages.theme.general.enable_robots_txt_editor'),
                    function (DashboardMenuSupport $menu): void {
                        $menu->registerItem(
                            DashboardMenuItem::make()
                                ->id('cms-core-appearance-robots-txt')
                                ->priority(6)
                                ->parentId('cms-core-appearance')
                                ->name('packages/theme::theme.robots_txt_editor')
                                ->icon('ti ti-file-type-txt')
                                ->route('theme.robots-txt')
                                ->permissions('theme.robots-txt')
                        );
                    }
                );
        });

        PanelSectionManager::default()->beforeRendering(function (): void {
            PanelSectionManager::registerItem(
                SettingCommonPanelSection::class,
                fn () => PanelSectionItem::make('website_tracking')
                    ->setTitle(trans('packages/theme::theme.settings.website_tracking.title'))
                    ->withIcon('ti ti-world')
                    ->withDescription(trans('packages/theme::theme.settings.website_tracking.description'))
                    ->withPriority(140)
                    ->withRoute('settings.website-tracking'),
            );
        });

        $this->app['events']->listen(RenderingAdminBar::class, function (): void {
            admin_bar()
                ->registerLink(trans('packages/theme::theme.name'), route('theme.index'), 'appearance', 'theme.index')
                ->registerLink(
                    trans('packages/theme::theme.theme_options'),
                    route('theme.options'),
                    'appearance',
                    'theme.options'
                );
        });

        $this->app->booted(function (): void {
            $this->app->register(HookServiceProvider::class);
        });

        $this->app->register(ThemeManagementServiceProvider::class);
        $this->app->register(EventServiceProvider::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ThemeActivateCommand::class,
                ThemeRemoveCommand::class,
                ThemeAssetsPublishCommand::class,
                ThemeOptionCheckMissingCommand::class,
                ThemeAssetsRemoveCommand::class,
                ThemeRenameCommand::class,
            ]);
        }
    }
}
