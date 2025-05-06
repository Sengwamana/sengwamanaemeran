<?php

namespace Botble\PluginManagement\Listeners;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Illuminate\Support\Str;

class PublishPluginAssets
{
    public function handle(): void
    {
        $pluginPath = plugin_path();

        foreach ($this->publishPaths() as $from => $to) {
            if (! Str::contains($from, $pluginPath)) {
                continue;
            }

            File::ensureDirectoryExists(dirname($to));
            File::copyDirectory($from, $to);
        }
    }

    private function publishPaths(): array
    {
        return IlluminateServiceProvider::pathsToPublish(null, 'cms-public');
    }
}
