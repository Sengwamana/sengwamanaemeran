<?php

namespace Botble\Setting\Enums;

use Botble\Base\Supports\Enum;

class DataRetentionPeriod extends Enum
{
    public const NEVER = 0;

    public const ONE_DAY = 1;

    public const THREE_DAYS = 3;

    public const ONE_WEEK = 7;

    public const ONE_MONTH = 30;

    public const THREE_MONTHS = 90;

    public const SIX_MONTHS = 180;

    public const ONE_YEAR = 365;

    protected static $langPath = 'core/setting::setting.enums.data_retention_period';
}
