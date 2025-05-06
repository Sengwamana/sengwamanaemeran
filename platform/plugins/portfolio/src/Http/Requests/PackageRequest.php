<?php

namespace Botble\Portfolio\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Rules\OnOffRule;
use Botble\Portfolio\Enums\PackageDuration;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class PackageRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'description' => ['nullable', 'string', 'max:400'],
            'content' => ['nullable', 'string', 'max:10000'],
            'duration' => ['required', 'string', Rule::in(PackageDuration::values())],
            'price' => ['required', 'string', 'min:0', 'max:1000000000'],
            'annual_price' => ['nullable', 'string', 'min:0', 'max:1000000000'],
            'features' => ['required', 'string', 'max:4000'],
            'status' => ['required', 'string', Rule::in(BaseStatusEnum::values())],
            'is_popular' => new OnOffRule(),
            'action_label' => ['nullable', 'string', 'max:255'],
            'action_url' => ['nullable', 'string', 'max:255'],
            'order' => ['required', 'numeric', 'min:0', 'max:99999'],
        ];
    }
}
