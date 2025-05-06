<?php

namespace Botble\Base\Traits\FieldOptions;

trait HasAspectRatio
{
    protected string $ratio = '';

    protected bool $withoutAspectRatio = false;

    public function aspectRatio(string $ratio): static
    {
        $this->ratio = $ratio;

        return $this;
    }

    public function withoutAspectRatio(): static
    {
        $this->withoutAspectRatio = true;

        return $this;
    }
}
