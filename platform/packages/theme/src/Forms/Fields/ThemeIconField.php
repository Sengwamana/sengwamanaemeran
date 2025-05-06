<?php

namespace Botble\Theme\Forms\Fields;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FormField;

class ThemeIconField extends FormField
{
    protected function getTemplate(): string
    {
        Assets::addScriptsDirectly('vendor/core/packages/theme/js/icons-field.js');

        return 'packages/theme::fields.icons-field';
    }
}
