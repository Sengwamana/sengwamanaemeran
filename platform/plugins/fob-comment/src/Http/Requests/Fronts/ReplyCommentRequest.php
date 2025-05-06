<?php

namespace FriendsOfBotble\Comment\Http\Requests\Fronts;

use Illuminate\Support\Arr;

class ReplyCommentRequest extends CommentRequest
{
    public function rules(): array
    {
        return Arr::except(parent::rules(), ['reference_id', 'reference_type', 'reference_url']);
    }
}
