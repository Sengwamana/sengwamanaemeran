<?php

namespace Botble\Translation\Http\Controllers;

use Botble\DataSynchronize\Http\Controllers\ImportController;
use Botble\DataSynchronize\Importer\Importer;
use Botble\Translation\Importers\ThemeTranslationImporter;

class ImportThemeTranslationController extends ImportController
{
    protected function getImporter(): Importer
    {
        return ThemeTranslationImporter::make();
    }
}
