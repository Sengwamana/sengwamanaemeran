<?php

namespace Botble\Base\Forms\FieldOptions;

class EmailFieldOption extends TextFieldOption
{
    public static function make(): static
    {
        return parent::make()
            ->label(trans('core/base::forms.email'))
            ->placeholder(trans('core/base::forms.email_placeholder'))
            ->maxLength(60);
    }
}
