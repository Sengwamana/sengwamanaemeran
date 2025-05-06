<?php

namespace Botble\Table\BulkChanges;

class CreatedAtBulkChange extends DateBulkChange
{
    public static function make(array $data = []): static
    {
        return parent::make()
            ->name('created_at')
            ->type('datePicker')
            ->title(trans('core/base::tables.created_at'));
    }
}
