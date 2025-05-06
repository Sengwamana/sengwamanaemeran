<?php

namespace Botble\Base\Forms\FieldOptions;

class IsFeaturedFieldOption extends CheckboxFieldOption
{
    public static function make(): static
    {
        return parent::make()
            ->label(trans('core/base::forms.is_featured'))
            ->defaultValue(0);
    }
}
