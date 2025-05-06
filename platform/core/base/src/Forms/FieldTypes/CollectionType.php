<?php

namespace Botble\Base\Forms\FieldTypes;

use Botble\Base\Traits\Forms\CanSpanColumns;

class CollectionType extends \Kris\LaravelFormBuilder\Fields\CollectionType
{
    use CanSpanColumns;
}
