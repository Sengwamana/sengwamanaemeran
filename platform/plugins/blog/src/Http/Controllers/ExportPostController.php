<?php

namespace Botble\Blog\Http\Controllers;

use Botble\Blog\Exporters\PostExporter;
use Botble\DataSynchronize\Exporter\Exporter;
use Botble\DataSynchronize\Http\Controllers\ExportController;

class ExportPostController extends ExportController
{
    protected function getExporter(): Exporter
    {
        return PostExporter::make();
    }
}
