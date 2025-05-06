<?php

namespace FriendsOfBotble\Comment\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static CommentStatus PENDING()
 * @method static CommentStatus APPROVED()
 * @method static CommentStatus SPAM()
 * @method static CommentStatus TRASH()
 */
class CommentStatus extends Enum
{
    public const PENDING = 'pending';

    public const APPROVED = 'approved';

    public const SPAM = 'spam';

    public const TRASH = 'trash';

    public static $langPath = 'plugins/fob-comment::comment.enums.statuses';

    public function toHtml(): string|HtmlString
    {
        return match ($this->value) {
            self::PENDING => Html::tag('span', self::PENDING()->label(), ['class' => 'badge bg-warning text-warning-fg']),
            self::APPROVED => Html::tag('span', self::APPROVED()->label(), ['class' => 'badge bg-success text-success-fg']),
            self::SPAM => Html::tag('span', self::SPAM()->label(), ['class' => 'badge bg-danger text-danger-fg']),
            self::TRASH => Html::tag('span', self::TRASH()->label(), ['class' => 'badge bg-secondary text-secondary-fg']),
            default => parent::toHtml(),
        };
    }
}
