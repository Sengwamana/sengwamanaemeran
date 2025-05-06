<?php

namespace Botble\Base\Exceptions;

use Botble\Base\Contracts\Exceptions\IgnoringReport;
use Illuminate\Http\Client\ConnectionException;

class CouldNotConnectToLicenseServerException extends ConnectionException implements IgnoringReport
{
}
