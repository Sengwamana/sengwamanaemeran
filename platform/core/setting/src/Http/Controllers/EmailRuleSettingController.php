<?php

namespace Botble\Setting\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Rules\DomainRule;
use Botble\Setting\Forms\EmailRulesSettingForm;
use Botble\Setting\Http\Requests\EmailRulesSettingRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmailRuleSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('core/setting::setting.email.email_rules'));

        return EmailRulesSettingForm::create()->renderForm();
    }

    public function update(EmailRulesSettingRequest $request): BaseHttpResponse
    {
        $validator = Validator::make([
            'email_rules_blacklist_email_domains' => $this->transformJsonToArray($request->input('email_rules_blacklist_email_domains')),
            'email_rules_blacklist_specified_emails' => $this->transformJsonToArray($request->input('email_rules_blacklist_specified_emails')),
            'email_rules_exception_emails' => $this->transformJsonToArray($request->input('email_rules_exception_emails')),
        ], [
            'email_rules_blacklist_email_domains' => ['nullable', 'array'],
            'email_rules_blacklist_email_domains.*' => ['required', new DomainRule()],
            'email_rules_blacklist_specified_emails' => ['nullable', 'array'],
            'email_rules_blacklist_specified_emails.*' => ['required', 'email'],
            'email_rules_exception_emails' => ['nullable', 'array'],
            'email_rules_exception_emails.*' => ['required', 'email'],
        ], attributes: [
            'email_rules_blacklist_email_domains.*' => 'Blacklist email domains',
            'email_rules_blacklist_specified_emails.*' => 'Blacklist specified emails',
            'email_rules_exception_emails.*' => 'Exception emails',
        ]);

        if ($validator->fails()) {
            return $this
                ->httpResponse()
                ->setError()
                ->withInput()
                ->setMessage($validator->errors()->first());
        }

        return $this->performUpdate($request->validated());
    }

    protected function transformJsonToArray(?string $json): array
    {
        if (! $json || ! Str::isJson($json)) {
            return [];
        }

        $data = json_decode($json, true);

        return array_map(fn ($item) => $item['value'], $data);
    }
}
