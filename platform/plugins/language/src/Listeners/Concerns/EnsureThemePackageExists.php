<?php

namespace Botble\Language\Listeners\Concerns;

use Botble\Theme\Theme;

trait EnsureThemePackageExists
{
    public function determineIfThemesExists(): bool
    {
        return class_exists(Theme::class);
    }
}
