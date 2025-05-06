<?php

namespace FriendsOfBotble\Comment\Http\Requests;

use Botble\Support\Http\Requests\Request;

class ReplyCommentRequest extends Request
{
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:1000'],
        ];
    }
}
