<?php

namespace Botble\Installer\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckIfInstalledMiddleware extends InstallerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($this->alreadyInstalled()) {
            return redirect()->to('/');
        }

        return $next($request);
    }
}
