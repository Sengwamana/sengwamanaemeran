<?php

namespace Botble\Contact\Enums;

use Botble\Base\Supports\Enum;

/**
 * @method static CustomFieldType TEXT()
 * @method static CustomFieldType NUMBER()
 * @method static CustomFieldType TEXTAREA()
 * @method static CustomFieldType DROPDOWN()
 * @method static CustomFieldType CHECKBOX()
 * @method static CustomFieldType RADIO()
 * @method static CustomFieldType DATE()
 * @method static CustomFieldType DATETIME()
 * @method static CustomFieldType TIME()
 */
class CustomFieldType extends Enum
{
    public const TEXT = 'text';

    public const NUMBER = 'number';

    public const TEXTAREA = 'textarea';

    public const DROPDOWN = 'dropdown';

    public const CHECKBOX = 'checkbox';

    public const RADIO = 'radio';

    public const DATE = 'date';

    public const DATETIME = 'datetime';

    public const TIME = 'time';

    public static $langPath = 'plugins/contact::contact.custom_field.enums.types';
}
