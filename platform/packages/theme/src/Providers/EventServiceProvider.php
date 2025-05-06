<?php

namespace Botble\Theme\Providers;

use Botble\Base\Events\FormRendering;
use Botble\Base\Events\SeederPrepared;
use Botble\Base\Events\SystemUpdateDBMigrated;
use Botble\Base\Events\SystemUpdatePublished;
use Botble\Theme\Listeners\AddFormJsValidation;
use Botble\Theme\Listeners\CoreUpdateThemeDB;
use Botble\Theme\Listeners\PublishThemeAssets;
use Botble\Theme\Listeners\SetDefaultTheme;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SystemUpdateDBMigrated::class => [
            CoreUpdateThemeDB::class,
        ],
        SystemUpdatePublished::class => [
            PublishThemeAssets::class,
        ],
        SeederPrepared::class => [
            SetDefaultTheme::class,
        ],
        FormRendering::class => [
            AddFormJsValidation::class,
        ],
    ];
}
