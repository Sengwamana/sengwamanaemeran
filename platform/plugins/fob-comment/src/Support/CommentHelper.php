<?php

namespace FriendsOfBotble\Comment\Support;

use Botble\Base\Contracts\BaseModel;
use Botble\Captcha\Facades\Captcha;
use FontLib\TrueType\Collection;
use FriendsOfBotble\Comment\Enums\CommentStatus;
use FriendsOfBotble\Comment\Models\Comment;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CommentHelper
{
    public static function isEnableReCaptcha(): bool
    {
        return is_plugin_active('captcha') && setting('fob_comment_enable_recaptcha', false) && Captcha::isEnabled();
    }

    public static function commentMustBeModerated(): bool
    {
        return setting('fob_comment_comment_moderation', false);
    }

    public static function getCommentOrder(): string
    {
        $order = setting('fob_comment_comment_order', 'asc');

        if (! in_array($order, ['asc', 'desc'])) {
            $order = 'asc';
        }

        return $order;
    }

    public static function isShowCommentCookieConsent(): bool
    {
        return setting('fob_comment_show_comment_cookie_consent', true);
    }

    public static function isAutoFillCommentForm(): bool
    {
        return setting('fob_comment_auto_fill_comment_form', true);
    }

    public static function isDisplayAdminBadge(): bool
    {
        return setting('fob_comment_display_admin_badge', true);
    }

    public static function getAuthorizedUser(): ?Authenticatable
    {
        $guard = match (true) {
            is_plugin_active('member') => 'member',
            is_plugin_active('real-estate') => 'account',
            is_plugin_active('ecommerce') => 'customer',
            default => null,
        };

        if (! $guard) {
            return null;
        }

        return auth($guard)->user();
    }

    public static function preparedDataForFill(): array
    {
        if (! CommentHelper::isAutoFillCommentForm()) {
            return [];
        }

        $data = [];

        $user = self::getAuthorizedUser();

        if ($user) {
            $data['name'] = $user->name;
            $data['email'] = $user->email;
        }

        return apply_filters('fob_comment_prepare_comment_data', $data);
    }

    public static function getCommentsCount(BaseModel $reference): int
    {
        $counter = app('fob.comments.counter');

        if (isset($counter[$reference::class][$reference->getKey()])) {
            return $counter[$reference->getKey()];
        }

        $counter = static::loadCommentsCount([$reference]);

        return $counter[$reference::class][$reference->getKey()] ?? 0;
    }

    public static function loadCommentsCount(Collection|array $collect): array
    {
        $counter = app('fob.comments.counter');

        if (empty($collect)) {
            return $counter;
        }

        $query = Comment::query();

        $query
            ->select(['reference_type', 'reference_id', DB::raw('count(*) as total')])
            ->where('status', CommentStatus::APPROVED);

        foreach ($collect as $reference) {
            $query->orWhere(function (Builder $query) use ($reference): void {
                $query
                    ->where('reference_type', $reference::class)
                    ->where('reference_id', $reference->getKey());
            });
        }

        $query->groupBy(['reference_type', 'reference_id']);

        $result = $query->get();

        foreach ($result as $item) {
            $counter[$item->reference_type][$item->reference_id] = $item->total;
        }

        app()->instance('fob.comments.counter', $counter);

        return $counter;
    }
}
