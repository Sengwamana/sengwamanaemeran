<?php

namespace Botble\Translation\Http\Controllers;

use Botble\DataSynchronize\Exporter\Exporter;
use Botble\DataSynchronize\Http\Controllers\ExportController;
use Botble\Translation\Exporters\OtherTranslationExporter;

class ExportOtherTranslationController extends ExportController
{
    protected function getExporter(): Exporter
    {
        return OtherTranslationExporter::make();
    }
}
