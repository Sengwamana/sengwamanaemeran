<?php

namespace Botble\Base\Forms\FieldTypes;

use Botble\Base\Traits\Forms\CanSpanColumns;

class SelectType extends \Kris\LaravelFormBuilder\Fields\SelectType
{
    use CanSpanColumns;
}
