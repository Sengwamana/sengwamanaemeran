<?php

namespace Botble\Captcha\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool verify(string $response, string $clientIp, array $options = [])
 * @method static string|null display(array $attributes = [], array $options = [])
 * @method static array rules()
 * @method static bool isEnabled()
 * @method static bool reCaptchaEnabled()
 * @method static bool mathCaptchaEnabled()
 * @method static array mathCaptchaRules()
 * @method static string captchaType()
 * @method static string reCaptchaType()
 * @method static array attributes()
 * @method static array scores()
 * @method static static registerFormSupport(string $form, string $request, string $title)
 * @method static array getFormsSupport()
 * @method static string|null formByRequest(string $request)
 * @method static string formSettingKey(string $form, string $key)
 * @method static mixed|null formSetting(string $form, string $key, mixed|null $default = false)
 *
 * @see \Botble\Captcha\Contracts\Captcha
 */
class Captcha extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'captcha';
    }
}
