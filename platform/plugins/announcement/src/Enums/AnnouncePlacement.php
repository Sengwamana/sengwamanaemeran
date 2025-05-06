<?php

namespace ArchiElite\Announcement\Enums;

use Botble\Base\Supports\Enum;

class AnnouncePlacement extends Enum
{
    public const TOP = 'top';

    public const BOTTOM = 'bottom';

    public const THEME = 'theme';

    protected static $langPath = 'plugins/announcement::announcements.enums.announce_placement';
}
