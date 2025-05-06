<?php

namespace Botble\Base\Forms\Fields;

class ColorSelectorField extends SelectField
{
    protected function getTemplate(): string
    {
        return 'core/base::forms.fields.color-selector';
    }
}
