<?php

namespace Botble\Base\Exceptions;

use RuntimeException;

class MissingZipExtensionException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('PHP Zip extension is not installed.');
    }
}
