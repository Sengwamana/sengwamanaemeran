<?php

namespace Botble\Table\Contracts;

use Botble\Base\Contracts\BaseModel;
use Botble\Table\Abstracts\TableAbstract;
use stdClass;

interface FormattedColumn
{
    public function formattedValue($value): ?string;

    public function renderCell(BaseModel|stdClass|array $item, TableAbstract $table): string;
}
