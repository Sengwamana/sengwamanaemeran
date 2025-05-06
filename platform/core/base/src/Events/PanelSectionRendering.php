<?php

namespace Botble\Base\Events;

use Botble\Base\Contracts\PanelSections\PanelSection;
use Illuminate\Foundation\Events\Dispatchable;

class PanelSectionRendering
{
    use Dispatchable;

    public function __construct(public PanelSection $section)
    {
    }
}
