<?php

namespace Botble\Base\Http\Middleware;

use Botble\Base\Facades\AdminHelper;
use Botble\Base\Supports\Language;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class LocaleMiddleware
{
    protected Application $app;

    public function __construct(Application $application)
    {
        $this->app = $application;
    }

    public function handle(Request $request, Closure $next)
    {
        if (AdminHelper::isInAdmin(true)) {
            return $next($request);
        }

        $this->app->setLocale(config('app.locale'));

        if (! $request->session()->has('site-locale')) {
            return $next($request);
        }

        $sessionLocale = $request->session()->get('site-locale');

        if (array_key_exists($sessionLocale, Language::getAvailableLocales())) {
            $this->app->setLocale($sessionLocale);
            $request->setLocale($sessionLocale);
        }

        return $next($request);
    }
}
