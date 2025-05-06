<?php

namespace Botble\Setting\Forms;

use Botble\Base\Forms\Fields\TagField;
use Botble\Setting\Http\Requests\EmailRulesSettingRequest;

class EmailRulesSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('core/setting::setting.email.email_rules'))
            ->setSectionDescription(trans('core/setting::setting.email.email_rules_description'))
            ->setValidatorClass(EmailRulesSettingRequest::class)
            ->add('email_rules_blacklist_email_domains', TagField::class, [
                'label' => trans('core/setting::setting.email.blacklist_email_domains'),
                'value' => setting('email_rules_blacklist_email_domains'),
                'help_block' => [
                    'text' => trans('core/setting::setting.email.blacklist_email_domains_helper'),
                ],
            ])
            ->add('email_rules_blacklist_specified_emails', TagField::class, [
                'label' => trans('core/setting::setting.email.blacklist_specified_emails'),
                'value' => setting('email_rules_blacklist_specified_emails'),
                'help_block' => [
                    'text' => trans('core/setting::setting.email.blacklist_specified_emails_helper'),
                ],
            ])
            ->add('email_rules_exception_emails', TagField::class, [
                'label' => trans('core/setting::setting.email.exception_emails'),
                'value' => setting('email_rules_exception_emails'),
                'help_block' => [
                    'text' => trans('core/setting::setting.email.exception_emails_helper'),
                ],
            ])
            ->add('email_rules_strict', 'onOffCheckbox', [
                'label' => trans('core/setting::setting.email.email_rules_strict'),
                'value' => setting('email_rules_strict', false),
                'help_block' => [
                    'text' => trans('core/setting::setting.email.email_rules_strict_helper'),
                ],
            ])
            ->add('email_rules_dns', 'onOffCheckbox', [
                'label' => trans('core/setting::setting.email.email_rules_dns'),
                'value' => setting('email_rules_dns', false),
                'help_block' => [
                    'text' => trans('core/setting::setting.email.email_rules_dns_helper'),
                ],
            ])
            ->add('email_rules_spoof', 'onOffCheckbox', [
                'label' => trans('core/setting::setting.email.email_rules_spoof'),
                'value' => setting('email_rules_spoof', false),
                'help_block' => [
                    'text' => trans('core/setting::setting.email.email_rules_spoof_helper'),
                ],
                'wrapper' => [
                    'class' => 'mb-0',
                ],
            ]);
    }
}
