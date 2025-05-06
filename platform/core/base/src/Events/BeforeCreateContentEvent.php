<?php

namespace Botble\Base\Events;

use Botble\Base\Contracts\BaseModel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class BeforeCreateContentEvent extends Event
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Request $request, public bool|BaseModel|null $data)
    {
    }
}
