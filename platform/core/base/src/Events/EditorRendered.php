<?php

namespace Botble\Base\Events;

use Illuminate\Foundation\Events\Dispatchable;

class EditorRendered
{
    use Dispatchable;

    public function __construct(
        public string $rendered,
        public string $name,
        public ?string $value = null,
        public bool $withShortcode = false,
        public array $attributes = []
    ) {
    }
}
