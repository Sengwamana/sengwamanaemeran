<?php

namespace Botble\Analytics\Http\Requests;

use Botble\Support\Http\Requests\Request;

class AnalyticsRequest extends Request
{
    public function rules(): array
    {
        return [
            'predefined_range' => ['nullable', 'string'],
            'changed_predefined_range' => ['nullable', 'boolean'],
        ];
    }
}
