<?php

namespace Botble\Base\Forms\FieldTypes;

use Botble\Base\Traits\Forms\CanSpanColumns;

class ChoiceType extends \Kris\LaravelFormBuilder\Fields\ChoiceType
{
    use CanSpanColumns;
}
