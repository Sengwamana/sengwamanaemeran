<?php

namespace Botble\Testimonial\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class TestimonialRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:250'],
            'company' => ['nullable', 'string', 'max:250'],
            'content' => ['required', 'string', 'max:1000'],
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
