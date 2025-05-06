<?php

namespace Botble\Base\Forms\Fields;

use Botble\Base\Forms\FieldTypes\FormField;

class OnOffField extends FormField
{
    protected bool $useDefaultFieldClass = false;

    protected function getTemplate(): string
    {
        return 'core/base::forms.fields.on-off';
    }
}
