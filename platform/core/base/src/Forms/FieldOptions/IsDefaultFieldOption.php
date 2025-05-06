<?php

namespace Botble\Base\Forms\FieldOptions;

class IsDefaultFieldOption extends CheckboxFieldOption
{
    public static function make(): static
    {
        return parent::make()
            ->label(trans('core/base::forms.is_default'))
            ->defaultValue(0);
    }
}
