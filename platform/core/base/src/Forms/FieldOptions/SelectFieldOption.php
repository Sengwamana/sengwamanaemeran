<?php

namespace Botble\Base\Forms\FieldOptions;

use Botble\Base\Forms\FormFieldOptions;
use Closure;
use Illuminate\Support\Collection;

class SelectFieldOption extends FormFieldOptions
{
    protected array|Collection $choices = [];

    protected array|string|bool|null $selected;

    protected bool $searchable = false;

    protected bool $multiple = false;

    protected string $emptyValue;

    protected bool $allowClear = false;

    public function choices(array|Collection|Closure $choices): static
    {
        $this->choices = $choices instanceof Closure ? $choices() : $choices;

        return $this;
    }

    public function getChoices(): array|Collection
    {
        return $this->choices;
    }

    public function selected(array|string|bool|float|null|Closure $selected): static
    {
        $this->selected = $selected instanceof Closure ? $selected() : $selected;

        return $this;
    }

    public function getSelected(): array|string|bool|null
    {
        return $this->selected;
    }

    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;

        if ($searchable) {
            if ($this->multiple) {
                $this->addAttribute('class', 'select-multiple');
            } else {
                $this->addAttribute('class', 'select-search-full');
            }
        }

        return $this;
    }

    public function ajaxSearch(): static
    {
        $this->addAttribute('class', 'select-search-ajax');

        return $this;
    }

    public function ajaxUrl(string $url): static
    {
        $this->addAttribute('data-url', $url);

        return $this;
    }

    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        if ($multiple) {
            $this->addAttribute('multiple', true);

            if ($this->searchable) {
                $this->addAttribute('class', 'select-multiple');
            }
        }

        return $this;
    }

    public function emptyValue(string|bool|int|null $value): static
    {
        $this->emptyValue = $value;

        return $this;
    }

    public function getEmptyValue(): string
    {
        return $this->emptyValue;
    }

    public function allowClear(bool $allowClear = true): static
    {
        $this->allowClear = $allowClear;

        return $this;
    }

    public function toArray(): array
    {
        $data = parent::toArray();

        $data['choices'] = $this->getChoices();

        if (isset($this->selected)) {
            $data['selected'] = $this->getSelected();

            if (is_array($this->selected) && ! empty(array_filter($this->selected))) {
                $data['attr']['data-selected'] = json_encode($this->getSelected());
            }
        } elseif (isset($this->defaultValue)) {
            $data['selected'] = $this->getDefaultValue();
        }

        if (isset($this->emptyValue) && $placeholder = $this->getEmptyValue()) {
            $data['attr']['placeholder'] = $placeholder;
            $data['attr']['data-placeholder'] = $placeholder;

        }

        if ($this->searchable) {
            $data['attr']['data-allow-clear'] = $this->allowClear ? 'true' : 'false';
        }

        return $data;
    }
}
