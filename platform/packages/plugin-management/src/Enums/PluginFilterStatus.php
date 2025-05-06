<?php

namespace Botble\PluginManagement\Enums;

use Botble\Base\Supports\Enum;

class PluginFilterStatus extends Enum
{
    public const ALL = 'all';

    public const ACTIVATED = 'activated';

    public const NOT_ACTIVATED = 'not-activated';

    public const UPDATES_AVAILABLE = 'updates-available';

    protected static $langPath = 'packages/plugin-management::plugin.enums.plugin_filter_status';
}
