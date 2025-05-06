<?php

namespace Botble\Base\Forms\FieldOptions;

class MultiChecklistFieldOption extends SelectFieldOption
{
    public function placeholder(string $placeholder): static
    {
        $this->addAttribute('placeholder', $placeholder);

        return $this;
    }

    public function toArray(): array
    {
        $data = parent::toArray();

        if (isset($this->emptyValue)) {
            $data['empty_value'] = $this->getEmptyValue();
        }

        return $data;
    }
}
