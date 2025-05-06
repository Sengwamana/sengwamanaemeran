<?php

namespace Botble\Base\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string decode($value)
 * @method static \Illuminate\Support\HtmlString script($url, $attributes = [], $secure = null)
 * @method static string attributes($attributes)
 * @method static \Illuminate\Support\HtmlString style($url, $attributes = [], $secure = null)
 * @method static \Illuminate\Support\HtmlString image($url, $alt = null, $attributes = [], $secure = null)
 * @method static \Illuminate\Support\HtmlString favicon($url, $attributes = [], $secure = null)
 * @method static \Illuminate\Support\HtmlString secureLink($url, $title = null, $attributes = [], $escape = true)
 * @method static \Illuminate\Support\HtmlString link($url, $title = null, $attributes = [], $secure = null, $escape = true)
 * @method static string entities($value)
 * @method static \Illuminate\Support\HtmlString linkSecureAsset($url, $title = null, $attributes = [], $escape = true)
 * @method static \Illuminate\Support\HtmlString linkAsset($url, $title = null, $attributes = [], $secure = null, $escape = true)
 * @method static \Illuminate\Support\HtmlString linkRoute($name, $title = null, $parameters = [], $attributes = [], $secure = null, $escape = true)
 * @method static \Illuminate\Support\HtmlString linkAction($action, $title = null, $parameters = [], $attributes = [], $secure = null, $escape = true)
 * @method static \Illuminate\Support\HtmlString mailto($email, $title = null, $attributes = [], $escape = true, bool $obfuscate = true)
 * @method static string email($email)
 * @method static string obfuscate($value)
 * @method static string nbsp($num = 1)
 * @method static \Illuminate\Support\HtmlString|string ol($list, $attributes = [])
 * @method static \Illuminate\Support\HtmlString|string ul($list, $attributes = [])
 * @method static \Illuminate\Support\HtmlString dl(array $list, array $attributes = [])
 * @method static \Illuminate\Support\HtmlString meta($name, $content, array $attributes = [])
 * @method static \Illuminate\Support\HtmlString tag($tag, $content, array $attributes = [])
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static mixed macroCall(string $method, array $parameters)
 * @method static void component($name, $view, array $signature)
 * @method static mixed componentCall($method, $parameters)
 * @method static bool hasComponent(string $name)
 *
 * @see \Kris\LaravelFormBuilder\Supports\HtmlBuilder
 */
class Html extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'html';
    }
}
