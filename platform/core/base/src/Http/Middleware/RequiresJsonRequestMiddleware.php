<?php

namespace Botble\Base\Http\Middleware;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequiresJsonRequestMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->expectsJson()) {
            return BaseHttpResponse::make()->setNextUrl(url('/'));
        }

        return $next($request);
    }
}
