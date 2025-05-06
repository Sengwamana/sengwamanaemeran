<?php

namespace Botble\Base\Forms\FieldOptions;

class TextareaFieldOption extends TextFieldOption
{
    public static function make(): static
    {
        return parent::make()
            ->maxLength(10000)
            ->rows(3);
    }

    public function rows(int $rows): static
    {
        $this->addAttribute('rows', $rows);

        return $this;
    }
}
