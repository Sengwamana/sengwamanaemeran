<?php

namespace Botble\Table\BulkChanges;

class IsFeaturedBulkChange extends SelectBulkChange
{
    public static function make(array $data = []): static
    {
        return parent::make()
            ->name('is_featured')
            ->title(trans('core/base::forms.is_featured'))
            ->choices(function () {
                return [
                    0 => trans('core/base::base.no'),
                    1 => trans('core/base::base.yes'),
                ];
            })
            ->validate('required|in:0,1');
    }
}
