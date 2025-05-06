<?php

namespace Botble\Captcha;

use Botble\Captcha\Contracts\Captcha as CaptchaContract;
use Botble\Captcha\Events\CaptchaRendered;
use Botble\Captcha\Events\CaptchaRendering;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class CaptchaV3 extends CaptchaContract
{
    protected bool $rendered = false;

    public function verify(string $response, string $clientIp, array $options = []): bool
    {
        if (! $this->reCaptchaEnabled()) {
            return true;
        }

        $response = Http::asForm()
            ->withoutVerifying()
            ->post(self::RECAPTCHA_VERIFY_API_URL, [
                'secret' => $this->secretKey,
                'response' => $response,
                'remoteip' => $clientIp,
            ]);

        $data = $response->json();

        if (! isset($data['success']) || ! $data['success']) {
            return false;
        }

        $action = $options[0];
        $minScore = isset($options[1]) ? (float) $options[1] : 0.6;

        if ($action && (! isset($data['action']) || $action != $data['action'])) {
            return false;
        }

        $score = $data['score'] ?? false;

        return $score && $score >= $minScore;
    }

    public function display(array $attributes = ['action' => 'form'], array $options = []): ?string
    {
        if (! $this->siteKey || ! $this->reCaptchaEnabled()) {
            return null;
        }

        $name = Arr::get($options, 'name', self::RECAPTCHA_INPUT_NAME);
        $uniqueId = uniqid($name . '-');
        $headContent = $this->headRender();
        $footerContent = $this->footerRender($uniqueId, $attributes);

        CaptchaRendering::dispatch($attributes, $options, $headContent, $footerContent);

        if (defined('THEME_FRONT_FOOTER')) {
            add_filter(THEME_FRONT_FOOTER, function (?string $html) use ($headContent, $footerContent): string {
                return $html . $headContent . $footerContent;
            }, 99);
        }

        add_filter(BASE_FILTER_FOOTER_LAYOUT_TEMPLATE, function (?string $html) use ($headContent, $footerContent): string {
            return $html . $headContent . $footerContent;
        }, 99);

        $captchaContent = view('plugins/captcha::v3.html', compact('name', 'uniqueId'))->render();

        $this->rendered = true;

        if (request()->ajax() || request()->wantsJson()) {
            $captchaContent .= $footerContent;
        }

        return tap(
            $captchaContent,
            fn (string $rendered) => CaptchaRendered::dispatch($rendered)
        );
    }

    protected function headRender(): string
    {
        return view('plugins/captcha::v3.head')->render();
    }

    protected function footerRender(string $uniqueId, array $attributes): string
    {
        $action = Arr::get($attributes, 'action', 'form');
        $isRendered = $this->rendered;

        $url = self::RECAPTCHA_CLIENT_API_URL . '?' . http_build_query([
                'onload' => 'onloadCallback',
                'render' => $this->siteKey,
                'hl' => app()->getLocale(),
            ]);

        return view('plugins/captcha::v3.script', [
            'siteKey' => $this->siteKey,
            'id' => $uniqueId,
            'action' => $action,
            'url' => $url,
            'isRendered' => $isRendered,
        ])->render();
    }
}
