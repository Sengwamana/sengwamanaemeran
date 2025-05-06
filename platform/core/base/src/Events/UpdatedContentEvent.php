<?php

namespace Botble\Base\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class UpdatedContentEvent extends Event
{
    use Dispatchable;
    use SerializesModels;

    public string $screen;

    public function __construct(string|Model $screen, public Request $request, public bool|Model|null $data)
    {
        if ($screen instanceof Model) {
            $screen = $screen->getTable();
        }

        $this->screen = $screen;
    }
}
