<?php

namespace Botble\Contact\Http\Requests;

use Botble\Base\Rules\OnOffRule;
use Botble\Contact\Enums\CustomFieldType;
use Botble\Contact\Models\CustomFieldOption;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CustomFieldRequest extends Request
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'options' => $this->isMultipleField() ? $this->input('options', []) : [],
        ]);
    }

    public function rules(): array
    {
        $requiredIfRule = Rule::when($this->isMultipleField(), 'required', 'nullable');

        return [
            'type' => ['required', 'string', Rule::in(CustomFieldType::values())],
            'name' => ['required', 'string', 'max:255'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'required' => [new OnOffRule()],
            'order' => ['required', 'integer', 'min:0', 'max:999'],
            'options' => [$requiredIfRule, 'array'],
            'options.*.id' => ['sometimes', 'string', Rule::exists(CustomFieldOption::class, 'id')],
            'options.*.label' => [$requiredIfRule, 'string'],
            'options.*.value' => [$requiredIfRule, 'string'],
            'options.*.order' => [$requiredIfRule, 'sometimes', 'numeric', 'min:0', 'max:999'],
        ];
    }

    protected function isMultipleField(): bool
    {
        return in_array($this->input('type'), [CustomFieldType::DROPDOWN, CustomFieldType::RADIO]);
    }
}
