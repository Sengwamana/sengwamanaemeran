<?php

namespace Botble\Table\BulkChanges;

use Botble\Table\Abstracts\TableBulkChangeAbstract;

class DateBulkChange extends TableBulkChangeAbstract
{
    public static function make(array $data = []): static
    {
        return parent::make()
            ->type('date')
            ->validate(['required', 'string', 'date']);
    }
}
