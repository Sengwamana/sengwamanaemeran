<?php

namespace Botble\Setting\Http\Requests;

use Botble\Base\Rules\EmailRule;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rules\RequiredIf;

class EmailSettingRequest extends Request
{
    public function rules(): array
    {
        $mailDriver = $this->input('email_driver');

        return apply_filters('cms_email_settings_validation_rules', [
            'email_driver' => ['required', 'in:smtp,mailgun,ses,postmark,log,array,sendmail'],
            'email_from_name' => ['required', 'string', 'max:150'],
            'email_from_address' => ['required', new EmailRule(), 'min:6', 'max:150'],
            'email_port' => ['nullable', 'numeric', $smtpRules = new RequiredIf($mailDriver == 'smtp')],
            'email_host' => ['nullable', 'string', $smtpRules],
            'email_username' => ['nullable', 'string'],
            'email_password' => ['nullable', 'string'],
            'email_local_domain' => ['nullable', 'string'],
            'email_encryption' => ['nullable', 'string'],
            'email_mail_gun_domain' => ['nullable', 'string', 'max:150', $mailgunRules = new RequiredIf($mailDriver == 'mailgun')],
            'email_mail_gun_secret' => ['nullable', 'string', $mailgunRules],
            'email_mail_gun_endpoint' => ['nullable', 'string', $mailgunRules],
            'email_ses_key' => ['nullable', 'string', $sesRules = new RequiredIf($mailDriver == 'ses')],
            'email_ses_secret' => ['nullable', 'string', $sesRules],
            'email_ses_region' => ['nullable', 'string', $sesRules],
            'email_postmark_token' => ['nullable', 'string', new RequiredIf($mailDriver == 'postmark')],
            'email_log_channel' => ['nullable', 'string', new RequiredIf($mailDriver == 'log')],
            'email_sendmail_path' => ['nullable', 'string', new RequiredIf($mailDriver == 'sendmail')],
        ]);
    }
}
