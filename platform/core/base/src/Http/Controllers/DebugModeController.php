<?php

namespace Botble\Base\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class DebugModeController extends BaseController
{
    public function postTurnOff(): BaseHttpResponse
    {
        $response = $this->httpResponse();

        if (! App::hasDebugModeEnabled()) {
            return $response
                ->setError()
                ->setMessage(__('The debug mode is already disabled.'));
        }

        $env = App::environmentFilePath();

        if (! File::isWritable($env)) {
            return $response
                ->setError()
                ->setMessage(__('The .env file is not writable.'));
        }

        $replaced = preg_replace(
            '/^APP_DEBUG=(.+)/m',
            'APP_DEBUG=false',
            $input = File::get($env)
        );

        if ($replaced === $input || $replaced === null) {
            return $response
                ->setError()
                ->setMessage(__('Unable to set debug mode. No APP_DEBUG variable was found in the .env file.'));
        }

        File::put($env, $replaced);

        File::delete(App::getCachedConfigPath());

        return $response
            ->setMessage(__('The debug mode has been disabled successfully.'));
    }
}
