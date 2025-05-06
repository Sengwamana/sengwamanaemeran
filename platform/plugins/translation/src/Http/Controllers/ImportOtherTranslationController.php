<?php

namespace Botble\Translation\Http\Controllers;

use Botble\DataSynchronize\Http\Controllers\ImportController;
use Botble\DataSynchronize\Importer\Importer;
use Botble\Translation\Importers\OtherTranslationImporter;

class ImportOtherTranslationController extends ImportController
{
    protected function getImporter(): Importer
    {
        return OtherTranslationImporter::make();
    }
}
