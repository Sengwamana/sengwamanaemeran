<?php

namespace Botble\Portfolio\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ServiceCategoryRequest extends Request
{
    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'string', 'exists:pf_service_categories,id'],
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'description' => ['nullable', 'string', 'max:400'],
            'image' => ['nullable', 'string'],
            'order' => ['required', 'numeric', 'min:0', 'max:99999'],
            'status' => ['required', 'string', Rule::in(BaseStatusEnum::values())],
        ];
    }
}
