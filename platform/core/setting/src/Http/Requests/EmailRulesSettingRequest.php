<?php

namespace Botble\Setting\Http\Requests;

use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;

class EmailRulesSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'email_rules_blacklist_email_domains' => ['nullable', 'string'],
            'email_rules_blacklist_specified_emails' => ['nullable', 'string'],
            'email_rules_exception_emails' => ['nullable', 'string'],
            'email_rules_strict' => [new OnOffRule()],
            'email_rules_dns' => [new OnOffRule()],
            'email_rules_spoof' => [new OnOffRule()],
        ];
    }
}
