<?php

use Botble\PluginManagement\Services\PluginService;

if (! function_exists('plugin_path')) {
    function plugin_path(?string $path = null): string
    {
        return platform_path('plugins' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
    }
}

if (! function_exists('is_plugin_active')) {
    function is_plugin_active(string $alias): bool
    {
        return in_array($alias, get_active_plugins());
    }
}

if (! function_exists('get_active_plugins')) {
    function get_active_plugins(): array
    {
        return PluginService::getActivatedPlugins();
    }
}

if (! function_exists('get_installed_plugins')) {
    function get_installed_plugins(): array
    {
        return PluginService::getInstalledPlugins();
    }
}
