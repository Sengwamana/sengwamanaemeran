<?php

namespace Botble\Base\Forms\FieldOptions;

class TagFieldOption extends SelectFieldOption
{
    protected array|string|bool|null $value = null;

    public function ajaxUrl(string $url): static
    {
        $this->addAttribute('data-url', $url);

        return $this;
    }

    public function placeholder(string $placeholder): static
    {
        $this->addAttribute('placeholder', $placeholder);

        return $this;
    }

    public function value(array|string|bool|null $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function toArray(): array
    {
        $data = parent::toArray();

        if ($this->value) {
            $data['value'] = $this->value;
        }

        return $data;
    }
}
