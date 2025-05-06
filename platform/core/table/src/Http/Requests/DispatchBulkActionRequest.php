<?php

namespace Botble\Table\Http\Requests;

use Botble\Support\Http\Requests\Request;

class DispatchBulkActionRequest extends Request
{
    public function rules(): array
    {
        return [
            'bulk_action' => ['sometimes', 'required', 'boolean'],
            'bulk_action_table' => ['required_with:bulk_action', 'string'],
            'bulk_action_target' => ['required_with:bulk_action', 'string'],
            'ids' => ['required_with:bulk_action', 'array'],
            'ids.*' => ['required', 'string'],
        ];
    }
}
