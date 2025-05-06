<?php

namespace Botble\PluginManagement\Listeners;

use Illuminate\Support\Facades\File;

class CoreUpdatePluginsDB
{
    public function handle(): void
    {
        $migrator = app('migrator');

        foreach (get_active_plugins() as $plugin) {
            $pluginPath = plugin_path($plugin);

            if (! File::isDirectory($pluginPath)) {
                continue;
            }

            $pluginMigrationPath = $pluginPath . '/database/migrations';

            if (! File::isDirectory($pluginMigrationPath)) {
                continue;
            }

            $migrator->run($pluginMigrationPath);
        }
    }
}
