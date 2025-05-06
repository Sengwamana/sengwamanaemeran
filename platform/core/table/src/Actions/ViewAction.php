<?php

namespace Botble\Table\Actions;

class ViewAction extends Action
{
    public static function make(string $name = 'view'): static
    {
        return parent::make($name)
            ->label(trans('core/base::tables.view'))
            ->color('info')
            ->icon('ti ti-eye');
    }
}
