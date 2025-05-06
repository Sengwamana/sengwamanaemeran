<?php

namespace Botble\Base\Exceptions;

use Botble\Base\Contracts\Exceptions\IgnoringReport;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Throwable;

class DisabledInDemoModeException extends BadRequestException implements IgnoringReport
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(trans('core/base::system.disabled_in_demo_mode') . $message, $code, $previous);
    }
}
