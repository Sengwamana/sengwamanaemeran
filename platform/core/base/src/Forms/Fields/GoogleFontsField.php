<?php

namespace Botble\Base\Forms\Fields;

class GoogleFontsField extends SelectField
{
    protected function getTemplate(): string
    {
        return 'core/base::forms.fields.google-fonts';
    }
}
