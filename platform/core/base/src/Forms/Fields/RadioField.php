<?php

namespace Botble\Base\Forms\Fields;

use Botble\Base\Forms\FieldTypes\SelectType;

class RadioField extends SelectType
{
    protected function getTemplate(): string
    {
        return 'core/base::forms.fields.custom-radio';
    }

    public function getDefaults(): array
    {
        return [
            ...parent::getDefaults(),
            'attr' => ['class' => null, 'id' => $this->getName()],
        ];
    }
}
