<?php

namespace Botble\Theme\Http\Requests;

use Botble\Support\Http\Requests\Request;

class CustomJsRequest extends Request
{
    public function rules(): array
    {
        return [
            'custom_header_js' => ['nullable', 'string', 'max:10000'],
            'custom_body_js' => ['nullable', 'string', 'max:10000'],
            'custom_footer_js' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
