<?php

namespace Botble\Table\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Table\TableBuilder;

class TableController extends BaseController
{
    public function __construct(protected TableBuilder $tableBuilder)
    {
    }
}
