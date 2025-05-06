<?php

namespace Botble\Theme\Database\Traits;

use Botble\Setting\Facades\Setting;
use Botble\Theme\Facades\ThemeOption;

trait HasThemeOptionSeeder
{
    protected function truncateOptions(): void
    {
        Setting::newQuery()->where('key', 'LIKE', ThemeOption::getOptionKey('%'))->delete();
    }

    protected function createThemeOptions(array $options, bool $truncate = true): void
    {
        if ($truncate) {
            $this->truncateOptions();
        }

        Setting::set(ThemeOption::prepareFromArray($options));

        Setting::save();
    }
}
