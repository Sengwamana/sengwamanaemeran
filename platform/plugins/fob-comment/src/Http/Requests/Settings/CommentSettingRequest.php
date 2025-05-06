<?php

namespace FriendsOfBotble\Comment\Http\Requests\Settings;

use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;

class CommentSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'fob_comment_enable_recaptcha' => [new OnOffRule()],
            'fob_comment_comment_moderation' => [new OnOffRule()],
            'fob_comment_show_comment_cookie_consent' => [new OnOffRule()],
            'fob_comment_auto_fill_comment_form' => [new OnOffRule()],
            'fob_comment_comment_order' => ['required', 'in:asc,desc'],
            'fob_comment_display_admin_badge' => [new OnOffRule()],
            'fob_comment_show_admin_role_name_for_admin_badge' => [new OnOffRule()],
        ];
    }
}
