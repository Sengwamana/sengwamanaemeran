<?php

namespace Botble\Contact\Http\Requests;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Rules\EmailRule;
use Botble\Base\Rules\OnOffRule;
use Botble\Base\Rules\PhoneNumberRule;
use Botble\Contact\Enums\CustomFieldType;
use Botble\Contact\Models\CustomField;
use Botble\Support\Http\Requests\Request;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class ContactRequest extends Request
{
    protected $errorBag = 'contact';

    protected array $mandatoryFields;

    protected array $displayFields;

    protected Collection $customFields;

    public function mandatoryFields(array $fields): static
    {
        $this->mandatoryFields = $fields;

        return $this;
    }

    public function displayFields(array $fields): static
    {
        $this->displayFields = $fields;

        return $this;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:40'],
            'email' => ['nullable', new EmailRule(), 'max:80'],
            'content' => ['required', 'string', 'max:10000'],
            'phone' => ['nullable', new PhoneNumberRule()],
            'address' => ['nullable', 'string', 'max:500'],
            'subject' => ['nullable', 'string', 'max:500'],
            'agree_terms_and_policy' => ['sometimes', 'accepted:1'],
        ];

        try {
            $rules = $this->applyRules(
                $rules,
                $this->request->getString('display_fields'),
                $this->request->getString('required_fields')
            );
        } catch (Throwable $exception) {
            BaseHelper::logError($exception);
        }

        $customFields = $this->getCustomFields();

        if ($customFields->isNotEmpty()) {
            $rules['contact_custom_fields'] = ['required', 'array'];
        }

        foreach ($customFields as $customField) {
            $customFieldRules = [$customField->required ? 'required' : 'nullable'];

            $rules["contact_custom_fields.$customField->id"] = match ($customField->type->getValue()) {
                CustomFieldType::TEXT, CustomFieldType::DROPDOWN, CustomFieldType::RADIO => [...$customFieldRules, 'string', 'max:255'],
                CustomFieldType::TEXTAREA => [...$customFieldRules, 'string', 'max:1000'],
                CustomFieldType::NUMBER => [...$customFieldRules, 'numeric'],
                CustomFieldType::CHECKBOX => [new OnOffRule()],
                default => $customFieldRules,
            };
        }

        return apply_filters('contact_request_rules', $rules, $this);
    }

    public function attributes(): array
    {
        $attributes = [
            'name' => __('Name'),
            'email' => __('Email'),
            'phone' => __('Phone'),
            'content' => __('Content'),
            'subject' => __('Subject'),
            'address' => __('Address'),
            'agree_terms_and_policy' => __('Agree terms and policy'),
        ];

        $customFields = $this->getCustomFields();

        foreach ($customFields as $customField) {
            $attributes["contact_custom_fields.$customField->id"] = $customField->name;
        }

        return $attributes;
    }

    public function messages(): array
    {
        return [
            'agree_terms_and_policy.accepted' => __('You must agree to the terms and policy.'),
        ];
    }

    protected function getCustomFields(): Collection
    {
        if (isset($this->customFields)) {
            return $this->customFields;
        }

        return $this->customFields = CustomField::query()->wherePublished()->with('options')->get();
    }

    protected function filtersByDisplayFields(array $rules): array
    {
        if (empty($this->displayFields)) {
            $this->displayFields = ['email', 'address', 'phone', 'subject'];
        }

        $displayFields = [...$this->displayFields, ...$this->alwaysMandatoryFields()];

        foreach ($rules as $key => $rule) {
            if (! in_array($key, $displayFields)) {
                unset($rules[$key]);
            }
        }

        return $rules;
    }

    protected function applyMandatoryFields(array $rules): array
    {
        if (empty($this->mandatoryFields)) {
            $this->mandatoryFields = ['email'];
        }

        $mandatoryFields = [...$this->mandatoryFields, ...$this->alwaysMandatoryFields()];

        foreach ($rules as $key => $rule) {
            if (is_array($rule)) {
                foreach ($rule as $ruleKey => $ruleValue) {
                    if (in_array($ruleValue, ['required', 'nullable'])) {
                        $rule[$ruleKey] = in_array($key, $mandatoryFields) ? 'required' : 'nullable';
                    }

                    $rules[$key] = $rule;
                }
            }
        }

        foreach ($mandatoryFields as $mandatoryField) {
            if (! in_array($mandatoryField, array_keys($rules))
                && in_array($mandatoryField, $this->displayFields)) {
                $rules[$mandatoryField] = 'required';
            }
        }

        return $rules;
    }

    protected function alwaysMandatoryFields(): array
    {
        return ['name', 'content', 'agree_terms_and_policy'];
    }

    public function applyRules(array $rules, ?string $displayFields, ?string $mandatoryFields): array
    {
        $this->mandatoryFields(array_filter(explode(',', (string) $mandatoryFields)));
        $this->displayFields(array_filter(explode(',', (string) $displayFields)));

        $rules = $this->filtersByDisplayFields($rules);

        return $this->applyMandatoryFields($rules);
    }
}
