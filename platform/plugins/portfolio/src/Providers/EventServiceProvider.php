<?php

namespace Botble\Portfolio\Providers;

use Botble\Portfolio\Listeners\PortfolioSiteMapListener;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        RenderingSiteMapEvent::class => [
            PortfolioSiteMapListener::class,
        ],
    ];
}
