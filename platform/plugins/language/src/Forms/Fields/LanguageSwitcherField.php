<?php

namespace Botble\Language\Forms\Fields;

use Botble\Base\Forms\FormField;

class LanguageSwitcherField extends FormField
{
    protected function getTemplate(): string
    {
        return 'plugins/language::forms.fields.language-switcher';
    }
}
