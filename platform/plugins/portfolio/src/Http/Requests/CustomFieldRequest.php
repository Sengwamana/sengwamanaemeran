<?php

namespace Botble\Portfolio\Http\Requests;

use Botble\Base\Rules\OnOffRule;
use Botble\Portfolio\Enums\CustomFieldType;
use Botble\Portfolio\Models\CustomField;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CustomFieldRequest extends Request
{
    public function rules(): array
    {
        $isDropdownField = $this->input('type') === CustomFieldType::DROPDOWN;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(CustomField::class)->ignore($this->custom_field, 'id'),
            ],
            'required' => ['nullable', new OnOffRule()],
            'placeholder' => ['nullable', 'string'],
            'type' => ['required', 'string', Rule::in(CustomFieldType::values())],
            'options.*.id' => [
                'sometimes',
            ],
            'options.*.label' => [
                'nullable',
                'string',
                Rule::requiredIf(fn () => $isDropdownField),
            ],
            'options.*.value' => [
                'nullable',
                'string',
            ],
            'options.*.order' => [
                'numeric',
                'min:0',
                'max:999',
            ],
        ];
    }
}
