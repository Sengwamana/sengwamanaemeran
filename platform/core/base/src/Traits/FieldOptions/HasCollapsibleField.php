<?php

namespace Botble\Base\Traits\FieldOptions;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasCollapsibleField
{
    public function collapsible(string $target, mixed $value = null, mixed $currentValue = null): static
    {
        if ($value === null) {
            return $this;
        }

        $styles = [
            Arr::get($this->getWrapperAttributes(), 'style'),
        ];

        $shouldDisplay = is_array($value) ? in_array($currentValue, $value) : $value == $currentValue;

        if (! $shouldDisplay) {
            $styles[] = 'display: none';
        }

        $styles = array_filter($styles);

        $this->wrapperAttributes([
            'data-bb-collapse' => 'true',
            'data-bb-trigger' => Str::startsWith($target, ['.', '#']) ? $target : "[name=$target]",
            'data-bb-value' => is_array($value) ? json_encode($value) : $value,
            'style' => $styles ? implode(';', $styles) : '',
        ]);

        return $this;
    }

    /**
     * @deprecated Use collapsible() instead.
     */
    public function collapseTrigger(string $trigger, array|bool|string|int $value, bool $isShow = true): static
    {
        $this->collapsible($trigger, $value, $isShow ? $value : null);

        return $this;
    }
}
