<?php

namespace Botble\Theme\Listeners;

use Botble\Setting\Facades\Setting;
use Botble\Theme\Facades\Theme;

class SetDefaultTheme
{
    public function handle(): void
    {
        Setting::forceSet('theme', Theme::getThemeName())->set('show_admin_bar', 1)->save();
    }
}
