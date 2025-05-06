<?php

namespace Botble\Base\Supports\Builders;

trait HasColor
{
    protected string $color = '';
    protected string $colorText = '#fff';

    public function color(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function colorText(string $color): static
    {
        $this->colorText = $color;

        return $this;
    }

    public function getColor(): string
    {
        return match ($this->color) {
            'primary' => 'btn-primary',
            'success' => 'btn-success',
            'info' => 'btn-info',
            'warning' => 'btn-warning',
            'danger' => 'btn-danger',
            'secondary' => 'btn-secondary',
            default => '',
        };
    }
}
