<?php

namespace Botble\Theme\ThemeOption\Fields;

use Botble\Theme\ThemeOption\ThemeOptionField;

class NumberField extends ThemeOptionField
{
    public function fieldType(): string
    {
        return 'number';
    }

    public function toArray(): array
    {
        $attributes = parent::toArray()['attributes']['options'] ?? [];

        return [
            ...parent::toArray(),
            'attributes' => [
                'name' => $this->name,
                'value' => $this->getValue(),
                'options' => [
                    ...$attributes,
                    'class' => 'form-control',
                ],
            ],
        ];
    }
}
