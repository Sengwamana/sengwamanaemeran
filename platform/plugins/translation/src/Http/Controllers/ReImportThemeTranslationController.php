<?php

namespace Botble\Translation\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Translation\Manager;

class ReImportThemeTranslationController extends BaseController
{
    public function __invoke(Manager $manager)
    {
        $manager->updateThemeTranslations();

        return $this->httpResponse()->setMessage(trans('plugins/translation::translation.import_success_message'));
    }
}
