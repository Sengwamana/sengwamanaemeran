<?php

namespace Botble\ACL\Http\Requests;

use Botble\Support\Http\Requests\Request;

class AssignRoleRequest extends Request
{
    public function rules(): array
    {
        return [
            'pk' => ['required', 'exists:users,id', 'min:1'],
            'value' => ['required', 'exists:roles,id', 'min:1'],
        ];
    }
}
