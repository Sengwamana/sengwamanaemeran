<?php

namespace Botble\Base\Forms\FieldOptions;

class NameFieldOption extends TextFieldOption
{
    public static function make(): static
    {
        return parent::make()
            ->label(trans('core/base::forms.name'))
            ->placeholder(trans('core/base::forms.name_placeholder'))
            ->maxLength(250);
    }
}
