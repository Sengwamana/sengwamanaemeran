<?php

namespace Botble\Theme\ThemeOption\Fields;

use Botble\Theme\ThemeOption\ThemeOptionField;

class RepeaterField extends ThemeOptionField
{
    protected array $fields = [];

    public function fieldType(): string
    {
        return 'repeater';
    }

    public function fields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'attributes' => [
                ...parent::toArray()['attributes'],
                'value' => $this->value ?: $this->defaultValue,
                'fields' => array_map(
                    fn (ThemeOptionField|array $field) => $field instanceof ThemeOptionField ? $field->toArray() : $field,
                    $this->fields
                ),
            ],
        ];
    }
}
