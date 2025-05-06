<?php

namespace FriendsOfBotble\Comment\Http\Requests;

use Botble\Base\Rules\EmailRule;
use Botble\Support\Http\Requests\Request;
use FriendsOfBotble\Comment\Enums\CommentStatus;
use Illuminate\Validation\Rule;

class CommentRequest extends Request
{
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:1000'],
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', new EmailRule(), 'max:120'],
            'website' => ['nullable', 'url', 'max:255'],
            'status' => ['required', Rule::in(CommentStatus::values())],
        ];
    }
}
