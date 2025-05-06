<?php

namespace Botble\PluginManagement\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Supports\DashboardMenuItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\PluginManagement\PluginManifest;
use Composer\Autoload\ClassLoader;

class PluginManagementServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('packages/plugin-management')
            ->loadAndPublishConfigurations(['permissions', 'general'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadHelpers()
            ->publishAssets();

        $manifest = (new PluginManifest())->getManifest();

        $loader = new ClassLoader();

        foreach ($manifest['namespaces'] as $key => $namespace) {
            $loader->setPsr4($namespace, plugin_path($key . '/src'));
        }

        $loader->register();

        foreach ($manifest['providers'] as $provider) {
            if (! class_exists($provider)) {
                continue;
            }

            $this->app->register($provider);
        }

        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->when(config('packages.plugin-management.general.enable_plugin_manager', true), function (): void {
                    DashboardMenu::make()
                        ->when(config('packages.plugin-management.general.enable_marketplace_feature', true), function (): void {
                            DashboardMenu::make()
                                ->registerItem(
                                    DashboardMenuItem::make()
                                        ->id('cms-core-plugins')
                                        ->priority(3000)
                                        ->name('packages/plugin-management::plugin.plugins')
                                        ->icon('ti ti-plug')
                                        ->permissions('plugins.index')
                                )
                                ->registerItem(
                                    DashboardMenuItem::make()
                                        ->id('cms-core-plugins-installed')
                                        ->priority(1)
                                        ->parentId('cms-core-plugins')
                                        ->name('packages/plugin-management::plugin.installed_plugins')
                                        ->icon('ti ti-square-check')
                                        ->route('plugins.index')
                                )
                                ->registerItem(
                                    DashboardMenuItem::make()
                                        ->id('cms-core-plugins-marketplace')
                                        ->priority(2)
                                        ->parentId('cms-core-plugins')
                                        ->name('packages/plugin-management::plugin.add_new_plugin')
                                        ->icon('ti ti-square-rounded-plus')
                                        ->route('plugins.new')
                                        ->permissions('plugins.marketplace')
                                );
                        }, function (): void {
                            DashboardMenu::make()
                                ->registerItem(
                                    DashboardMenuItem::make()
                                        ->id('cms-core-plugins')
                                        ->priority(3000)
                                        ->name('packages/plugin-management::plugin.plugins')
                                        ->icon('ti ti-plug')
                                        ->route('plugins.index')
                                );
                        });
                });
        });

        $this->app->register(CommandServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(HookServiceProvider::class);
    }
}
