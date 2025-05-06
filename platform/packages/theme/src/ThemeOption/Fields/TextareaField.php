<?php

namespace Botble\Theme\ThemeOption\Fields;

class TextareaField extends TextField
{
    protected int $rows = 3;

    public function fieldType(): string
    {
        return 'textarea';
    }

    public function rows(int $rows): static
    {
        $this->rows = $rows;

        return $this;
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'attributes' => [
                ...parent::toArray()['attributes'],
                'options' => [
                    ...parent::toArray()['attributes']['options'],
                    'rows' => $this->rows,
                ],
            ],
        ];
    }
}
