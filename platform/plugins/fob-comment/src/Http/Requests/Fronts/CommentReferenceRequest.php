<?php

namespace FriendsOfBotble\Comment\Http\Requests\Fronts;

use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CommentReferenceRequest extends Request
{
    public function rules(): array
    {
        return [
            'reference_id' => [Rule::when($this->has('reference_type'), 'required', 'nullable'), 'string'],
            'reference_type' => [Rule::when($this->has('reference_id'), 'required', 'nullable'), 'string'],
            'reference_url' => [Rule::when(! $this->has('reference_id') && ! $this->has('reference_type'), 'required', 'nullable'), 'string'],
        ];
    }
}
