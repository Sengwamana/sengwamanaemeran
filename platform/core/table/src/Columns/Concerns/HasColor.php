<?php

namespace Botble\Table\Columns\Concerns;

use Botble\Base\Facades\Html;
use Closure;

trait HasColor
{
    protected string $color;

    public function color(string|Closure $color): static
    {
        $this->color = $color instanceof Closure ? $color() : $color;

        return $this;
    }

    public function hasColor(): bool
    {
        return isset($this->color);
    }

    public function applyColor($value): string
    {
        if (! $value) {
            return '';
        }

        if (! $this->hasColor()) {
            return $value;
        }

        return Html::tag('span', $value, ['class' => 'text-' . $this->color]);
    }
}
