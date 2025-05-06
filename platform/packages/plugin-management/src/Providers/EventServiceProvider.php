<?php

namespace Botble\PluginManagement\Providers;

use Botble\Base\Events\SeederPrepared;
use Botble\Base\Events\SystemUpdateDBMigrated;
use Botble\Base\Events\SystemUpdatePublished;
use Botble\Base\Listeners\ClearDashboardMenuCaches;
use Botble\PluginManagement\Events\ActivatedPluginEvent;
use Botble\PluginManagement\Listeners\ActivateAllPlugins;
use Botble\PluginManagement\Listeners\ClearPluginCaches;
use Botble\PluginManagement\Listeners\CoreUpdatePluginsDB;
use Botble\PluginManagement\Listeners\PublishPluginAssets;
use Illuminate\Contracts\Database\Events\MigrationEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        MigrationEvent::class => [
            ClearPluginCaches::class,
        ],
        SystemUpdateDBMigrated::class => [
            CoreUpdatePluginsDB::class,
        ],
        SystemUpdatePublished::class => [
            PublishPluginAssets::class,
        ],
        SeederPrepared::class => [
            ActivateAllPlugins::class,
        ],
        ActivatedPluginEvent::class => [
            ClearDashboardMenuCaches::class,
        ],
    ];
}
