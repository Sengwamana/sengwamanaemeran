<?php

namespace Botble\Captcha\Events;

use Illuminate\Foundation\Events\Dispatchable;

class CaptchaRendering
{
    use Dispatchable;

    public function __construct(
        public array $attributes = [],
        public array $options = [],
        public string $head = '',
        public string $footer = ''
    ) {
    }
}
