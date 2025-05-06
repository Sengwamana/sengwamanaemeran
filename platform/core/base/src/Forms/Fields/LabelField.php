<?php

namespace Botble\Base\Forms\Fields;

use Botble\Base\Forms\FormField;

class LabelField extends FormField
{
    protected function getDefaults(): array
    {
        return [
            'label' => '',
        ];
    }

    protected function getTemplate(): string
    {
        return 'core/base::forms.fields.label';
    }
}
