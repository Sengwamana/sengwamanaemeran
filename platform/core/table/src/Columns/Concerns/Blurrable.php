<?php

namespace Botble\Table\Columns\Concerns;

use Botble\Table\Columns\FormattedColumn;

trait Blurrable
{
    protected bool $blurred = false;

    protected int|string $blurredRadius;

    public function initializeBlurrable(): void
    {
        $this->getValueUsing(
            fn (FormattedColumn $column, mixed $value) => $column->applyBlurrableIfAvailable($value)
        );
    }

    public function blurry(int|string $radius = 5): static
    {
        $this->blurred = true;
        $this->blurredRadius = is_numeric($radius) ? "{$radius}px" : $radius;

        return $this;
    }

    public function applyBlurrableIfAvailable(mixed $text): mixed
    {
        if (! $text || ! $this->blurred || ! $this->blurredRadius) {
            return $text;
        }

        return $this->applyBlurrable($text);
    }

    public function applyBlurrable(mixed $text): mixed
    {
        if (! is_string($text)) {
            return $text;
        }

        return sprintf('<span style="filter: blur(%s);">%s</span>', $this->blurredRadius, $text);
    }
}
