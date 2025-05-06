<?php

namespace Botble\Base\Forms\FieldOptions;

class EditorFieldOption extends TextareaFieldOption
{
    public static function make(): static
    {
        return parent::make()
            ->maxLength(100000)
            ->rows(4);
    }

    public function allowedShortcodes(bool $allowedShortcodes = true): static
    {
        $this->addAttribute('with-short-code', $allowedShortcodes);

        return $this;
    }
}
