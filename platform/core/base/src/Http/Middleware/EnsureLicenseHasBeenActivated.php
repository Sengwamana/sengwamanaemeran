<?php

namespace Botble\Base\Http\Middleware;

use Botble\Base\Supports\Core;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureLicenseHasBeenActivated
{
    public function __construct(private Core $core)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        if (
            ! is_in_admin(true)
            || Auth::guest()
            || $this->core->isSkippedLicenseReminder()
            || $this->core->verifyLicense(true, 15)
        ) {
            return $next($request);
        }

        $whitelistRoutes = [
            'unlicensed',
            'unlicensed.skip',
            'settings.license.activate',
        ];

        if (! $request->routeIs($whitelistRoutes)) {
            return redirect()->route('unlicensed', ['redirect_url' => $request->fullUrl()]);
        }

        return $next($request);
    }

}
