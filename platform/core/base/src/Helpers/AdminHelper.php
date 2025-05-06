<?php

namespace Botble\Base\Helpers;

use Botble\Base\Facades\BaseHelper;
use Closure;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AdminHelper
{
    public function registerRoutes(
        Closure|callable $closure,
        array $middleware = ['web', 'core', 'auth']
    ): RouteRegistrar {
        return Route::prefix(BaseHelper::getAdminPrefix())->middleware($middleware)->group(fn () => $closure());
    }

    public function isInAdmin(bool $force = false): bool
    {
        $prefix = BaseHelper::getAdminPrefix();

        if (empty($prefix)) {
            return true;
        }

        $segments = array_slice(request()->segments(), 0, count(explode('/', $prefix)));

        $isInAdmin = implode('/', $segments) === $prefix;

        return $force ? $isInAdmin : apply_filters(IS_IN_ADMIN_FILTER, $isInAdmin);
    }

    public function themeMode(): string
    {
        $default = 'light';

        if (! Auth::check()) {
            return $default;
        }

        return Auth::user()->getMeta('theme_mode', $default);
    }

    public function isPreviewing(): bool
    {
        return Auth::check() && app('request')->input('preview');
    }
}
