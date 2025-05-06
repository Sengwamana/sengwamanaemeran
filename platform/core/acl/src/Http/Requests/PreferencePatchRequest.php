<?php

namespace Botble\ACL\Http\Requests;

use Botble\Support\Http\Requests\Request;

class PreferencePatchRequest extends Request
{
    public function rules(): array
    {
        return [
            'minimal_sidebar' => ['sometimes', 'required', 'in:yes,no'],
        ];
    }
}
