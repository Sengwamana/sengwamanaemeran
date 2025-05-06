<?php

namespace Botble\Base\Forms\FieldOptions;

use Botble\Base\Forms\FormFieldOptions;

class MediaImagesFieldOption extends FormFieldOptions
{
    protected array|string|bool|null $selected = null;

    public static function make(): static
    {
        return parent::make()
            ->label(trans('core/base::forms.images'));
    }

    public function selected(array|string|bool|null $selected): static
    {
        $this->selected = $selected;

        return $this;
    }

    public function values(array|string|bool|null $selected): static
    {
        return $this->selected($selected);
    }

    public function getSelected(): array|string|bool|null
    {
        return $this->selected;
    }

    public function toArray(): array
    {
        $data = parent::toArray();

        if (isset($this->selected)) {
            $data['selected'] = $this->getSelected();
        }

        $data['values'] = $data['selected'] ?? $this->getSelected();

        return $data;
    }
}
