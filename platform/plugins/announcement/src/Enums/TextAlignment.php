<?php

namespace ArchiElite\Announcement\Enums;

use Botble\Base\Supports\Enum;

class TextAlignment extends Enum
{
    public const START = 'start';

    public const CENTER = 'center';

    protected static $langPath = 'plugins/announcement::announcements.enums.text_alignment';
}
