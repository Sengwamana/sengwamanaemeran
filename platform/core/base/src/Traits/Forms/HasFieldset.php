<?php

namespace Botble\Base\Traits\Forms;

use Botble\Base\Facades\Html;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

trait HasFieldset
{
    public function addOpenFieldset(string $name, Arrayable|array $attributes = []): static
    {
        $attributes = $attributes instanceof Arrayable ? $attributes->toArray() : $attributes;
        $attributes = [
            'class' => 'form-fieldset',
            ...$attributes,
        ];

        $this->add(
            sprintf('open_fieldset_%s', $name),
            HtmlField::class,
            HtmlFieldOption::make()
                ->content(sprintf('<fieldset %s>', Html::attributes($attributes)))
                ->toArray()
        );

        return $this;
    }

    public function addCloseFieldset(string $name): static
    {
        $this->add(
            sprintf('close_fieldset_%s', $name),
            HtmlField::class,
            HtmlFieldOption::make()
                ->content('</fieldset>')
                ->toArray()
        );

        return $this;
    }

    public function addOpenCollapsible(string $name, mixed $value = null, mixed $currentValue = null, Arrayable|array $attributes = []): static
    {
        $attributes = $attributes instanceof Arrayable ? $attributes->toArray() : $attributes;

        $attributes = [
            'data-bb-collapse' => 'true',
            'data-bb-trigger' => Str::startsWith($name, ['.', '#']) ? $name : "[name=$name]",
            'data-bb-value' => $value,
            ...$attributes,
        ];

        if ($value != $currentValue) {
            $attributes['style'] = 'display: none';
        }

        return $this->addOpenFieldset($name . '_' . $value, $attributes);
    }

    public function addCloseCollapsible(string $name, mixed $value = null): static
    {
        return $this->addCloseFieldset($name . '_' . $value);
    }
}
