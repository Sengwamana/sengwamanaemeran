<?php

namespace Botble\Base\Forms\FieldOptions;

class ImageFieldOption extends TextFieldOption
{
    public static function make(): static
    {
        return parent::make()
            ->label(trans('core/base::forms.image'));
    }
}
