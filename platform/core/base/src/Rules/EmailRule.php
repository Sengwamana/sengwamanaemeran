<?php

namespace Botble\Base\Rules;

use Closure;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\Extra\SpoofCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\NoRFCWarningsValidation;
use Egulias\EmailValidator\Validation\RFCValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class EmailRule implements ValidationRule
{
    protected bool $blacklist = true;

    protected bool $strict;

    protected bool $dns;

    protected bool $spoof;

    public function __construct()
    {
        $this->strict ??= setting('email_rules_strict', false);
        $this->dns ??= setting('email_rules_dns', false);
        $this->spoof ??= setting('email_rules_spoof', false);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) && ! (is_object($value) && method_exists($value, '__toString'))) {
            $fail(trans('validation.string'));

            return;
        }

        $exceptionEmails = $this->getSettingsFromJson('email_rules_exception_emails');

        if (! empty($exceptionEmails) && in_array(strtolower($value), $exceptionEmails)) {
            return;
        }

        $multiValidation = new MultipleValidationWithAnd($this->getEmailValidations());

        if (! (new EmailValidator())->isValid($value, $multiValidation)) {
            $fail(trans('validation.email'));
        }

        if ($this->blacklist) {
            $this->validateBlacklistEmailDomains($attribute, $value, $fail);
            $this->validateBlacklistSpecifiedEmails($attribute, $value, $fail);
        }
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function strict(bool $strict = true): static
    {
        $this->strict = $strict;

        return $this;
    }

    public function dns(bool $dns = true): static
    {
        $this->dns = $dns;

        return $this;
    }

    public function spoof(bool $spoof = true): static
    {
        $this->spoof = $spoof;

        return $this;
    }

    protected function validateBlacklistEmailDomains(string $attribute, mixed $value, Closure $fail): void
    {
        $domains = $this->getSettingsFromJson('email_rules_blacklist_email_domains');

        if (empty($domains)) {
            return;
        }

        $this->validateBlacklist(
            $domains,
            $attribute,
            Str::after(strtolower($value), '@'),
            $fail
        );
    }

    protected function validateBlacklistSpecifiedEmails(string $attribute, mixed $value, Closure $fail): void
    {
        $emails = $this->getSettingsFromJson('email_rules_blacklist_specified_emails');

        if (empty($emails)) {
            return;
        }

        $this->validateBlacklist($emails, $attribute, $value, $fail);
    }

    protected function validateBlacklist(array $blacklist, string $attribute, mixed $value, Closure $fail): void
    {
        if (in_array(strtolower($value), $blacklist)) {
            $fail(trans('core/base::base.validation.email_in_blacklist'));
        }
    }

    protected function getSettingsFromJson(string $key): array
    {
        $setting = setting($key);

        if (! $setting || ! Str::isJson($setting)) {
            return [];
        }

        return array_map(fn (array $item) => $item['value'], json_decode($setting, true));
    }

    public function getEmailValidations(): array
    {
        $validations = [];

        if ($this->strict) {
            $validations[] = new NoRFCWarningsValidation();
        }

        if ($this->dns && function_exists('idn_to_ascii')) {
            $validations[] = new DNSCheckValidation();
        }

        if ($this->spoof && extension_loaded('intl')) {
            $validations[] = new SpoofCheckValidation();
        }

        if (empty($validations)) {
            $validations[] = new RFCValidation();
        }

        return $validations;
    }
}
