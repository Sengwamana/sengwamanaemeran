<?php

namespace Botble\Icon\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Icon\Commands\IconUpdateCommand;
use Botble\Icon\Facades\Icon as IconFacade;
use Botble\Icon\IconManager;
use Botble\Icon\View\Components\Icon;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Blade;

class IconServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this
            ->setNamespace('core/icon')
            ->loadAndPublishConfigurations('icon');

        $this->app->singleton(IconManager::class);
    }

    public function boot(): void
    {
        Blade::component('core::icon', Icon::class);

        $aliasLoader = AliasLoader::getInstance();

        if (! class_exists('CoreIcon')) {
            $aliasLoader->alias('CoreIcon', IconFacade::class);
        }

        if ($this->app->runningInConsole()) {
            $this->commands([IconUpdateCommand::class]);
        }
    }
}
