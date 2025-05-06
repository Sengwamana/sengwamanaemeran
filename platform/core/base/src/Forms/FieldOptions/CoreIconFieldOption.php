<?php

namespace Botble\Base\Forms\FieldOptions;

class CoreIconFieldOption extends TextFieldOption
{
    public static function make(): static
    {
        return parent::make()
                ->label(trans('core/base::forms.icon'))
                ->addAttribute('data-placeholder', trans('core/base::forms.icon_placeholder'))
                ->addAttribute('data-allow-clear', 'true')
                ->maxLength(120);
    }
}
