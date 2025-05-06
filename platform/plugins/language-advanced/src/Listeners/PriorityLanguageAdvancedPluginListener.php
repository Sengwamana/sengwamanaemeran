<?php

namespace Botble\LanguageAdvanced\Listeners;

use Botble\Base\Facades\BaseHelper;
use Botble\LanguageAdvanced\Plugin;
use Exception;

class PriorityLanguageAdvancedPluginListener
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
