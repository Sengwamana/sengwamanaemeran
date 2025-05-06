<?php

namespace Botble\Portfolio\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static QuoteStatus UNREAD()
 * @method static QuoteStatus READ()
 */
class QuoteStatus extends Enum
{
    public const READ = 'read';
    public const UNREAD = 'unread';

    public static $langPath = 'plugins/portfolio::portfolio.enums.quote_statuses';

    public function toHtml(): HtmlString|string
    {
        return match ($this->value) {
            self::UNREAD => Html::tag('span', self::UNREAD()->label(), ['class' => 'badge bg-warning text-warning-fg'])
                ->toHtml(),
            self::READ => Html::tag('span', self::READ()->label(), ['class' => 'badge bg-success text-success-fg'])
                ->toHtml(),
            default => parent::toHtml(),
        };
    }
}
