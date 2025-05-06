<?php

namespace Botble\Theme\Events;

use Botble\Base\Events\Event;

class RenderingSiteMapEvent extends Event
{
    public function __construct(public ?string $key = null)
    {
    }
}
