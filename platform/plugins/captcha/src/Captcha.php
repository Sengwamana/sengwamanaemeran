<?php

namespace Botble\Captcha;

use Botble\Captcha\Contracts\Captcha as CaptchaContract;
use Botble\Captcha\Events\CaptchaRendered;
use Botble\Captcha\Events\CaptchaRendering;
use Illuminate\Support\Facades\Http;

class Captcha extends CaptchaContract
{
    protected bool $rendered = false;

    public function display(array $attributes = [], array $options = []): ?string
    {
        if (! $this->siteKey || ! $this->reCaptchaEnabled()) {
            return null;
        }

        $name = 'captcha_' . md5(uniqid((string) rand(), true));

        $headContent = $this->headRender();
        $footerContent = $this->footerRender($name);

        CaptchaRendering::dispatch($attributes, $options, $headContent, $footerContent);

        add_filter(['theme-front-header', 'ecommerce_checkout_header'], function ($html) use ($headContent) {
            return $html . $headContent;
        }, 299);

        add_filter(['theme-front-footer', 'ecommerce_checkout_footer'], function (?string $html) use ($footerContent): string {
            return $html . $footerContent;
        }, 99);

        add_filter(BASE_FILTER_HEAD_LAYOUT_TEMPLATE, function ($html) use ($headContent) {
            return $html . $headContent;
        }, 299);

        add_filter(BASE_FILTER_FOOTER_LAYOUT_TEMPLATE, function (?string $html) use ($footerContent): string {
            return $html . $footerContent;
        }, 99);

        $this->rendered = true;

        $captchaContent = view('plugins/captcha::v2.html', ['name' => $name, 'siteKey' => $this->siteKey])->render();

        if (request()->ajax()) {
            $captchaContent .= $footerContent;
        }

        return
            tap(
                $captchaContent,
                fn (string $rendered) => CaptchaRendered::dispatch($rendered)
            );
    }

    public function verify(string $response, string $clientIp = null, array $options = []): bool
    {
        if (! $this->reCaptchaEnabled()) {
            return true;
        }

        if (empty($response)) {
            return false;
        }

        $response = Http::asForm()
            ->withoutVerifying()
            ->post(self::RECAPTCHA_VERIFY_API_URL, [
                'secret' => $this->secretKey,
                'response' => $response,
                'remoteip' => $clientIp,
            ]);

        return $response->json('success');
    }

    protected function headRender(): string
    {
        return view('plugins/captcha::header-meta')->render();
    }

    protected function footerRender(string $name): string
    {
        $isRendered = $this->rendered;

        $url = self::RECAPTCHA_CLIENT_API_URL . '?' . http_build_query([
                'onload' => 'onloadCallback',
                'render' => 'explicit',
                'hl' => app()->getLocale(),
            ]);

        return view('plugins/captcha::v2.script', compact('url', 'isRendered', 'name'))->render();
    }
}
