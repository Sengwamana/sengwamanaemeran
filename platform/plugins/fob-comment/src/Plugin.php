<?php

namespace FriendsOfBotble\Comment;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Models\Setting;
use Illuminate\Support\Facades\Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Schema::dropIfExists('fob_comments');

        Setting::query()
            ->where('key', 'like', 'fob_comment_%')
            ->delete();
    }
}
