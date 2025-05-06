<?php

namespace Botble\Base\Forms\FieldOptions;

class DescriptionFieldOption extends TextareaFieldOption
{
    public static function make(): static
    {
        return parent::make()
            ->label(trans('core/base::forms.description'))
            ->placeholder(trans('core/base::forms.description_placeholder'))
            ->maxLength(400)
            ->rows(4);
    }
}
