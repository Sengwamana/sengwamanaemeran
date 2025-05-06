<?php

namespace Botble\Base\Supports\Builders;

trait HasLabel
{
    protected string|bool $label = '';

    public function label(string|bool $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string|bool
    {
        return $this->label;
    }
}
