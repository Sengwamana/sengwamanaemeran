<?php

use Botble\Base\Http\Middleware\RequiresJsonRequestMiddleware;
use Botble\Shortcode\Http\Controllers\ShortcodeController;
use Botble\Theme\Facades\Theme;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Theme::registerRoutes(function (): void {
    Route::post('ajax/render-ui-blocks', [ShortcodeController::class, 'ajaxRenderUiBlock'])
        ->name('public.ajax.render-ui-block')
        ->middleware(RequiresJsonRequestMiddleware::class)
        ->withoutMiddleware(VerifyCsrfToken::class);
});
