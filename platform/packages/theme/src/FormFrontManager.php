<?php

namespace Botble\Theme;

use Botble\Support\Http\Requests\Request;
use Illuminate\Support\Facades\App;
use LogicException;

class FormFrontManager
{
    protected static array $forms = [];

    protected static array $formRequests = [];

    public static function register(string $form, string $request): void
    {
        $hasDebugModeEnabled = App::hasDebugModeEnabled() && App::isLocal();

        if (! class_exists($form)) {
            throw_if(
                $hasDebugModeEnabled,
                new LogicException(sprintf('Form [%s] does not exists.', $form))
            );

            return;
        }

        if (! is_subclass_of($form, FormFront::class)) {
            throw_if(
                $hasDebugModeEnabled,
                new LogicException(sprintf('Form [%s] must be an instance of [%s]', $form, FormFront::class))
            );

            return;
        }

        if (! class_exists($request)) {
            throw_if(
                $hasDebugModeEnabled,
                new LogicException(sprintf('Request [%s] does not exists.', $request))
            );

            return;
        }

        if (! is_subclass_of($request, Request::class)) {
            throw_if(
                $hasDebugModeEnabled,
                new LogicException(sprintf('Request [%s] must be an instance of [%s]', $form, Request::class))
            );

            return;
        }

        static::$forms[] = $form;

        if ($request) {
            static::$formRequests[$form] = $request;
        }
    }

    public static function remove(string $form): void
    {
        if (($key = array_search($form, static::$forms)) !== false) {
            unset(static::$forms[$key]);
        }

        if (isset(static::$formRequests[$form])) {
            unset(static::$formRequests[$form]);
        }
    }

    public static function forms(): array
    {
        return static::$forms;
    }

    public static function formRequestOf(string $form): ?string
    {
        return static::$formRequests[$form] ?? null;
    }

    public static function formByRequest(string $request): ?string
    {
        return array_search($request, static::$formRequests);
    }
}
