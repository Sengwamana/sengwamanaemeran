<?php

namespace Botble\Portfolio\Enums;

use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static PackageDuration HOURLY()
 * @method static PackageDuration DAILY()
 * @method static PackageDuration WEEKLY()
 * @method static PackageDuration MONTHLY()
 * @method static PackageDuration ANNUALLY()
 * @method static PackageDuration QUARTERLY()
 */
class PackageDuration extends Enum
{
    public const MONTHLY = 'monthly';

    public const HOURLY = 'hourly';

    public const DAILY = 'daily';

    public const WEEKLY = 'weekly';

    public const QUARTERLY = 'quarterly';

    public const ANNUALLY = 'annually';

    public const ONE_TIME = 'one_time';

    public static $langPath = 'plugins/portfolio::portfolio.enums.package_durations';

    public function toHtml(): HtmlString|string
    {
        return $this->label();
    }

    public function shortLabel(): string
    {
        return match ($this->value) {
            self::HOURLY => __('Hour'),
            self::DAILY => __('Day'),
            self::WEEKLY => __('Week'),
            self::MONTHLY => __('Month'),
            self::ANNUALLY => __('Annual'),
            self::QUARTERLY => __('Quarter'),
            self::ONE_TIME => __('One time'),
            default => '',
        };
    }
}
