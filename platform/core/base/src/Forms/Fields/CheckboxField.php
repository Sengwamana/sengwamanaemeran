<?php

namespace Botble\Base\Forms\Fields;

use Botble\Base\Forms\FieldTypes\CheckableType;

class CheckboxField extends CheckableType
{
    protected function getTemplate(): string
    {
        return 'core/base::forms.fields.checkbox';
    }
}
