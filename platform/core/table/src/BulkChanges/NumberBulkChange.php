<?php

namespace Botble\Table\BulkChanges;

use Botble\Table\Abstracts\TableBulkChangeAbstract;

class NumberBulkChange extends TableBulkChangeAbstract
{
    public static function make(array $data = []): static
    {
        return parent::make()
            ->type('number')
            ->validate(['required', 'integer', 'min:0']);
    }
}
