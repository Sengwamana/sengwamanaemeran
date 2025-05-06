<?php

namespace Botble\Theme\Providers;

use Botble\Theme\Facades\Theme;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Move base routes to a service provider to make sure all filters & actions can hook to base routes
     */
    public function boot(): void
    {
        $this->app->booted(function (): void {
            $this->loadRoutesFromTheme(Theme::getThemeName());

            if (Theme::hasInheritTheme()) {
                $this->loadRoutesFromTheme(Theme::getInheritTheme());
            }
        });
    }

    protected function loadRoutesFromTheme(string $theme): void
    {
        $routeFilePath = theme_path($theme . '/routes/web.php');

        if ($routeFilePath && $this->app['files']->exists($routeFilePath)) {
            $this->loadRoutesFrom($routeFilePath);
        }
    }
}
