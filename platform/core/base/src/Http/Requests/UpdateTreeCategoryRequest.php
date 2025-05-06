<?php

namespace Botble\Base\Http\Requests;

use Botble\Support\Http\Requests\Request;

class UpdateTreeCategoryRequest extends Request
{
    public function rules(): array
    {
        return [
            'data' => ['required', 'array'],
            'data.*.id' => ['required', 'regex:/^[a-zA-Z0-9-]+$/'],
            'data.*.name' => ['required', 'string'],
            'data.*.children' => ['sometimes', 'array'],
        ];
    }
}
