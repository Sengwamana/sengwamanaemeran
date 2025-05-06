<?php

namespace Botble\Theme\Supports;

use Closure;
use Illuminate\Support\Str;

class FormFrontHelper
{
    public static function addFormStart(
        string|array|Closure $callback,
        int $priority = 20,
        int $arguments = 2
    ): void {
        add_filter('form_front_form_start', $callback, $priority, $arguments);
    }

    public static function addFormEnd(
        string|array|Closure $callback,
        int $priority = 20,
        int $arguments = 2
    ): void {
        add_filter('form_front_form_end', $callback, $priority, $arguments);
    }

    public static function addBeforeFormSubmitButton(
        string|array|Closure $callback,
        int $priority = 20,
        int $arguments = 2
    ): void {
        add_filter('form_front_before_submit_button', $callback, $priority, $arguments);
    }

    public static function settingKey(string $form, string $key): string
    {
        return 'form_front_' . $key . '_' . str_replace('\\', '', Str::snake($form));
    }

    public static function setting(string $form, string $key, mixed $default = false): mixed
    {
        return setting(static::settingKey($form, $key), $default);
    }
}
