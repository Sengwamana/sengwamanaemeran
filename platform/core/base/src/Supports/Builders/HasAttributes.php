<?php

namespace Botble\Base\Supports\Builders;

use Illuminate\Support\Arr;

trait HasAttributes
{
    protected array $attributes = [];

    public function attributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function addAttribute(string $attribute, $value): static
    {
        $this->attributes[$attribute] = $value;

        return $this;
    }

    public function removeAttribute(string $attribute): static
    {
        Arr::forget($this->attributes, $attribute);

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $attribute, string|array|null $default = null)
    {
        return Arr::get($this->attributes, $attribute, $default);
    }
}
