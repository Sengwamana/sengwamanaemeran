<?php

namespace Botble\Base\Forms\Fields;

use Botble\Base\Forms\FormField;

class NumberField extends FormField
{
    protected function getTemplate(): string
    {
        return 'core/base::forms.fields.number';
    }
}
