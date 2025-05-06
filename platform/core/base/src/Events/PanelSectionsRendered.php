<?php

namespace Botble\Base\Events;

use Botble\Base\Contracts\PanelSections\Manager;
use Illuminate\Foundation\Events\Dispatchable;

class PanelSectionsRendered
{
    use Dispatchable;

    public function __construct(public Manager $panelSectionManager)
    {
    }
}
