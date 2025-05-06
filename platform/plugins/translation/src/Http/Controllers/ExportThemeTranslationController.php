<?php

namespace Botble\Translation\Http\Controllers;

use Botble\DataSynchronize\Exporter\Exporter;
use Botble\DataSynchronize\Http\Controllers\ExportController;
use Botble\Translation\Exporters\ThemeTranslationExporter;

class ExportThemeTranslationController extends ExportController
{
    protected function getExporter(): Exporter
    {
        return ThemeTranslationExporter::make();
    }
}
