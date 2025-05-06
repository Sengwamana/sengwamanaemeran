<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\Html;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Supports\Core;
use Botble\Base\Supports\DashboardMenu as DashboardMenuSupport;
use Botble\Base\Supports\Editor;
use Botble\Base\Supports\PageTitle as PageTitleSupport;

if (! function_exists('language_flag')) {
    function language_flag(?string $flag, ?string $name = null, int $width = 16): string
    {
        if (! $flag) {
            return '';
        }

        $flag = apply_filters('cms_language_flag', $flag, $name);

        $flagPath = BASE_LANGUAGE_FLAG_PATH . $flag . '.svg';

        if (file_exists(public_path($flagPath))) {
            $contents = file_get_contents(public_path($flagPath));

            $contents = trim(preg_replace('/^(<\?xml.+?\?>)/', '', $contents));

            return str_replace(
                '<svg',
                rtrim(sprintf('<svg style="height: %spx; width: auto;" title="%s" class="flag" loading="lazy"', $width, $name)),
                $contents
            );
        }

        return Html::image(asset($flagPath), sprintf('%s flag', $name), [
            'title' => $name,
            'class' => 'flag',
            'style' => "height: {$width}px",
            'loading' => 'lazy',
        ]);
    }
}

if (! function_exists('render_editor')) {
    function render_editor(
        string $name,
        ?string $value = null,
        bool $withShortCode = false,
        array $attributes = []
    ): string {
        return (new Editor())->registerAssets()->render($name, $value, $withShortCode, $attributes);
    }
}

if (! function_exists('is_in_admin')) {
    function is_in_admin(bool $force = false): bool
    {
        return AdminHelper::isInAdmin($force);
    }
}

if (! function_exists('page_title')) {
    function page_title(): PageTitleSupport
    {
        return PageTitle::getFacadeRoot();
    }
}

if (! function_exists('dashboard_menu')) {
    function dashboard_menu(): DashboardMenuSupport
    {
        return DashboardMenu::getFacadeRoot();
    }
}

if (! function_exists('get_cms_version')) {
    function get_cms_version(): string
    {
        try {
            return Core::make()->version();
        } catch (Throwable) {
            return '...';
        }
    }
}

if (! function_exists('get_core_version')) {
    function get_core_version(): string
    {
        return '7.5.1';
    }
}

if (! function_exists('get_minimum_php_version')) {
    function get_minimum_php_version(): string
    {
        try {
            return Core::make()->minimumPhpVersion();
        } catch (Throwable) {
            return phpversion();
        }
    }
}

if (! function_exists('platform_path')) {
    function platform_path(?string $path = null): string
    {
        $path = ltrim($path, DIRECTORY_SEPARATOR);

        return base_path('platform' . ($path ? DIRECTORY_SEPARATOR . $path : ''));
    }
}

if (! function_exists('core_path')) {
    function core_path(?string $path = null): string
    {
        return platform_path('core' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
    }
}

if (! function_exists('package_path')) {
    function package_path(?string $path = null): string
    {
        return platform_path('packages' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
    }
}
