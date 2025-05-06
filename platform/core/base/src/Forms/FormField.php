<?php

namespace Botble\Base\Forms;

use Botble\Base\Traits\Forms\CanSpanColumns;
use Kris\LaravelFormBuilder\Fields\FormField as BaseFormField;

abstract class FormField extends BaseFormField
{
    use CanSpanColumns;

    protected bool $useDefaultFieldClass = true;

    protected array $defaultFieldAttributes = [];

    protected function getDefaults(): array
    {
        $attributes = parent::getDefaults();

        if ($this->defaultFieldAttributes) {
            $attributes['attr'] = [
                ...$attributes['attr'],
                $this->defaultFieldAttributes,
            ];
        }

        if (! $this->useDefaultFieldClass) {
            $attributes['attr']['class'] = null;
        }

        return $attributes;
    }
}
