<?php

namespace Botble\PluginManagement\Listeners;

use Botble\Base\Facades\BaseHelper;
use Botble\PluginManagement\Services\PluginService;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class ActivateAllPlugins
{
    public function handle(): void
    {
        try {
            $plugins = array_values(BaseHelper::scanFolder(plugin_path()));

            if (empty($plugins)) {
                return;
            }

            $pluginService = app(PluginService::class);

            foreach ($plugins as $plugin) {
                $pluginService->activate($plugin);
            }

            Artisan::call('migrate', ['--force' => true]);
        } catch (Throwable) {
        }
    }
}
