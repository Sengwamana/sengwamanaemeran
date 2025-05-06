<?php

namespace ArchiElite\Announcement;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Models\Setting;
use Illuminate\Support\Facades\Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Schema::dropIfExists('announcements_translations');
        Schema::dropIfExists('announcements');

        Setting::query()
            ->where('key', 'like', 'announcement_%')
            ->delete();
    }
}
