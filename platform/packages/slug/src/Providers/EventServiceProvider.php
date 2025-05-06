<?php

namespace Botble\Slug\Providers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\FinishedSeederEvent;
use Botble\Base\Events\SeederPrepared;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Slug\Listeners\CreatedContentListener;
use Botble\Slug\Listeners\CreateMissingSlug;
use Botble\Slug\Listeners\DeletedContentListener;
use Botble\Slug\Listeners\TruncateSlug;
use Botble\Slug\Listeners\UpdatedContentListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UpdatedContentEvent::class => [
            UpdatedContentListener::class,
        ],
        CreatedContentEvent::class => [
            CreatedContentListener::class,
        ],
        DeletedContentEvent::class => [
            DeletedContentListener::class,
        ],
        SeederPrepared::class => [
            TruncateSlug::class,
        ],
        FinishedSeederEvent::class => [
            CreateMissingSlug::class,
        ],
    ];
}
