<?php

namespace Botble\Portfolio\Enums;

use Botble\Base\Supports\Enum;

/**
 * @method static CustomFieldType TEXT()
 * @method static CustomFieldType DROPDOWN()
 * @method static CustomFieldType CHECKBOX()
 * @method static CustomFieldType TEXTAREA()
 */
class CustomFieldType extends Enum
{
    public const TEXT = 'text';

    public const NUMBER = 'number';

    public const DROPDOWN = 'dropdown';

    public const CHECKBOX = 'checkbox';

    public const TEXTAREA = 'textarea';

    public static $langPath = 'plugins/portfolio::portfolio.enums.fields';
}
