<?php

namespace Botble\Shortcode\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Botble\Shortcode\Shortcode register(string $key, string|null $name, string|null $description = null, $callback = null, string $previewImage = '')
 * @method static void remove(string $key)
 * @method static \Botble\Shortcode\Shortcode setPreviewImage(string $key, string $previewImage)
 * @method static \Botble\Shortcode\Shortcode enable()
 * @method static \Botble\Shortcode\Shortcode disable()
 * @method static \Illuminate\Support\HtmlString compile(string $value, bool $force = false)
 * @method static string|null strip(string|null $value)
 * @method static array getAll()
 * @method static void setAdminConfig(string $key, callable|array|string|null $html)
 * @method static void modifyAdminConfig(string $key, callable $callback)
 * @method static string generateShortcode(string $name, array $attributes = [], string|null $content = null, bool $lazy = false)
 * @method static \Botble\Shortcode\Compilers\ShortcodeCompiler getCompiler()
 * @method static \Botble\Shortcode\ShortcodeField fields()
 *
 * @see \Botble\Shortcode\Shortcode
 */
class Shortcode extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'shortcode';
    }
}
