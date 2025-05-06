<?php

namespace Botble\Backup\Providers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\ServiceProvider;
use Botble\Dashboard\Events\RenderingDashboardWidgets;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app['events']->listen(RenderingDashboardWidgets::class, function (): void {
            add_filter(DASHBOARD_FILTER_ADMIN_NOTIFICATIONS, [$this, 'registerAdminAlert'], 5);
        });
    }

    public function registerAdminAlert(?string $alert): string
    {
        if (! BaseHelper::hasDemoModeEnabled()) {
            return $alert;
        }

        return $alert . view('plugins/backup::partials.admin-alert')->render();
    }
}
