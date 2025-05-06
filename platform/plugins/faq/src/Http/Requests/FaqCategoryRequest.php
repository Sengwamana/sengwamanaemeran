<?php

namespace Botble\Faq\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class FaqCategoryRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:250'],
            'description' => ['nullable', 'string', 'max:300'],
            'order' => ['required', 'integer', 'min:0', 'max:127'],
            'status' => ['required', Rule::in(BaseStatusEnum::values())],
        ];
    }
}
