<?php

namespace Botble\Base\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Pipeline;

class CoreMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        return Pipeline::send($request)
            ->through(App::make('core.middleware') ?: [])
            ->then(function (Request $request) use ($next) {
                return $next($request);
            });
    }
}
