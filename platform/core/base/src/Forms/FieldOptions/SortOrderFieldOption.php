<?php

namespace Botble\Base\Forms\FieldOptions;

class SortOrderFieldOption extends TextFieldOption
{
    public static function make(): static
    {
        return parent::make()
            ->label(trans('core/base::forms.sort_order'))
            ->placeholder(trans('core/base::forms.order_by_placeholder'))
            ->defaultValue(0);
    }
}
