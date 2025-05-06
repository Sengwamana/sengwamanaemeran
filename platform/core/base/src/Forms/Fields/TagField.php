<?php

namespace Botble\Base\Forms\Fields;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FormField;

class TagField extends FormField
{
    protected function getTemplate(): string
    {
        Assets::addStyles('tagify')
            ->addScripts('tagify')
            ->addScriptsDirectly('vendor/core/core/base/js/tags.js');

        return 'core/base::forms.fields.tags';
    }
}
