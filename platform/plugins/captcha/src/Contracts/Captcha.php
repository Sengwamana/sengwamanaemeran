<?php

namespace Botble\Captcha\Contracts;

use Botble\Theme\FormFrontManager;
use Illuminate\Support\Str;

abstract class Captcha
{
    public const RECAPTCHA_CLIENT_API_URL = 'https://www.google.com/recaptcha/api.js';

    public const RECAPTCHA_VERIFY_API_URL = 'https://www.google.com/recaptcha/api/siteverify';

    public const RECAPTCHA_INPUT_NAME = 'g-recaptcha-response';

    protected array $forms = [];

    protected array $formRequests = [];

    public function __construct(protected ?string $siteKey, protected ?string $secretKey)
    {
    }

    abstract public function verify(string $response, string $clientIp, array $options = []): bool;

    abstract public function display(array $attributes = [], array $options = []): ?string;

    public function rules(): array
    {
        if (! $this->reCaptchaEnabled()) {
            return [];
        }

        return [self::RECAPTCHA_INPUT_NAME => ['required', 'captcha']];
    }

    public function isEnabled(): bool
    {
        return $this->reCaptchaEnabled();
    }

    public function reCaptchaEnabled(): bool
    {
        if (! $this->siteKey || ! $this->secretKey) {
            return false;
        }

        return (bool) setting('enable_captcha');
    }

    public function mathCaptchaEnabled(): bool
    {
        return (bool) setting('enable_math_captcha');
    }

    public function mathCaptchaRules(): array
    {
        return ['math-captcha' => ['required', 'string', 'math_captcha']];
    }

    public function captchaType(): string
    {
        return $this->reCaptchaType();
    }

    public function reCaptchaType(): string
    {
        return setting('captcha_type', 'v2') ?: 'v2';
    }

    public function attributes(): array
    {
        return [
            'captcha' => __('Captcha'),
            'math-captcha' => __('Math Captcha'),
        ];
    }

    public function scores(): array
    {
        $scores = [];

        foreach (range(1, 9) as $i) {
            $key = $i / 10;
            $scores[(string) $key] = (string) $key;
        }

        return $scores;
    }

    public function registerFormSupport(string $form, string $request, string $title): static
    {
        $this->forms[$form] = $title;
        $this->formRequests[$form] = $request;

        return $this;
    }

    public function getFormsSupport(): array
    {
        if (class_exists(FormFrontManager::class)) {
            foreach (FormFrontManager::forms() as $form) {
                $this->registerFormSupport($form, FormFrontManager::formRequestOf($form), $form::formTitle());
            }
        }

        return $this->forms;
    }

    public function formByRequest(string $request): ?string
    {
        return array_search($request, $this->formRequests);
    }

    public function formSettingKey(string $form, string $key): string
    {
        return $key . '_' . str_replace('\\', '', Str::snake($form));
    }

    public function formSetting(string $form, string $key, mixed $default = false): mixed
    {
        return setting($this->formSettingKey($form, $key), $default);
    }
}
