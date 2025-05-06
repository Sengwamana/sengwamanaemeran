<?php

namespace Botble\Language\Listeners;

use Botble\Base\Facades\BaseHelper;
use Botble\Language\Plugin;
use Exception;

class ActivatedPluginListener
{
    public function handle(): void
    {
        try {
            Plugin::activated();
        } catch (Exception $exception) {
            BaseHelper::logError($exception);
        }
    }
}
