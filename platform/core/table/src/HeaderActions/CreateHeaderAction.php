<?php

namespace Botble\Table\HeaderActions;

class CreateHeaderAction extends HeaderAction
{
    public static function make(string $name = 'create'): static
    {
        return parent::make($name)
            ->icon('ti ti-plus')
            ->label(trans('core/base::forms.create'))
            ->color('primary');
    }
}
