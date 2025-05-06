<?php

namespace Botble\Base\Http\Requests;

use Botble\Support\Http\Requests\Request;

class SelectSearchAjaxRequest extends Request
{
    public function rules(): array
    {
        return [
            'search' => ['required', 'string'],
            'page' => ['required', 'integer'],
        ];
    }
}
