<?php

namespace Botble\Blog\Http\Controllers;

use Botble\Blog\Importers\PostImporter;
use Botble\DataSynchronize\Http\Controllers\ImportController;
use Botble\DataSynchronize\Importer\Importer;

class ImportPostController extends ImportController
{
    protected function getImporter(): Importer
    {
        return PostImporter::make();
    }
}
