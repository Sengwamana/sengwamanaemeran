<?php

namespace Botble\Base\Http\Middleware;

use Botble\Base\Facades\AdminAppearance;
use Botble\Base\Facades\AdminHelper;
use Botble\Base\Supports\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! AdminHelper::isInAdmin(true)) {
            return $next($request);
        }

        $sessionLocale = $request->session()->get('site-locale');

        if (Auth::check()) {
            $userLocale = AdminAppearance::forUser(auth()->user())->getLocale() ?: $sessionLocale;
        } else {
            $userLocale = AdminAppearance::getLocale() ?: $sessionLocale;
        }

        if (array_key_exists($userLocale, Language::getAvailableLocales())) {
            app()->setLocale($userLocale);
            $request->setLocale($userLocale);
        }

        return $next($request);
    }
}
