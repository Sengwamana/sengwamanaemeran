<?php

namespace Botble\Slug\Events;

use Botble\Base\Events\Event;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class UpdatedPermalinkSettings extends Event
{
    use SerializesModels;

    public function __construct(public string $reference, public string $prefix, public Request $request)
    {
    }
}
