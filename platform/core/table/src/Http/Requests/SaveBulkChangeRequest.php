<?php

namespace Botble\Table\Http\Requests;

use Botble\Support\Http\Requests\Request;

class SaveBulkChangeRequest extends Request
{
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'string'],
            'key' => ['required', 'string'],
            'value' => ['nullable', 'string'],
            'class' => ['required', 'string'],
        ];
    }
}
