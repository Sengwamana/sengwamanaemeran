<?php

namespace Botble\Theme\ThemeOption\Fields;

use Botble\Theme\ThemeOption\ThemeOptionField;

class MultiCheckListField extends ThemeOptionField
{
    protected array $options = [];

    protected bool $inline = false;

    public function fieldType(): string
    {
        return 'customCheckbox';
    }

    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function inline(bool $inline = true): static
    {
        $this->inline = $inline;

        return $this;
    }

    public function getName(): string
    {
        $name = parent::getName();

        return str_ends_with($name, '[]') ? $name : "{$name}[]";
    }

    public function toArray(): array
    {
        $values = json_decode(theme_option($this->name), true) ?: $this->defaultValue;

        return [
            ...parent::toArray(),
            'attributes' => [
                'values' => collect($this->options)->map(function ($label, $value) use ($values) {
                    return [$this->getName(), $value, $label, in_array($value, $values)];
                })->values()->all(),
                'inline' => $this->inline,
            ],
        ];
    }
}
