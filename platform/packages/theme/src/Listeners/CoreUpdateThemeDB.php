<?php

namespace Botble\Theme\Listeners;

use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\File;

class CoreUpdateThemeDB
{
    public function handle(): void
    {
        $themePath = theme_path(Theme::getThemeName());

        if (! File::isDirectory($themePath)) {
            return;
        }

        $themeMigrationPath = $themePath . '/database/migrations';

        if (! File::isDirectory($themeMigrationPath)) {
            return;
        }

        app('migrator')->run($themeMigrationPath);
    }
}
