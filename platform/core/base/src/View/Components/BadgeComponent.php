<?php

namespace Botble\Base\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BadgeComponent extends Component
{
    public function __construct(
        public string $label,
        public string $color = 'primary',
        public ?string $icon = null
    ) {
    }

    public function render(): View|Closure|string
    {
        return $this->view('core/base::components.badge');
    }
}
