<?php

namespace Botble\Base\Forms\FieldOptions;

use Botble\Base\Forms\FormFieldOptions;
use Closure;

class InputFieldOption extends FormFieldOptions
{
    protected array|float|string|bool|null $value;

    public function value(array|float|string|bool|null|Closure $value): static
    {
        $this->value = $value instanceof Closure ? $value() : $value;

        return $this;
    }

    public function getValue(): array|string|bool|null
    {
        return $this->value;
    }

    public function placeholder(string $placeholder): static
    {
        $this->addAttribute('placeholder', $placeholder);

        return $this;
    }

    public function toArray(): array
    {
        $data = parent::toArray();

        if (isset($this->value)) {
            $data['value'] = $this->getValue();
        }

        return $data;
    }
}
