<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Base\Http\Middleware\RequiresJsonRequestMiddleware;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Newsletter\Http\Controllers'], function (): void {
    AdminHelper::registerRoutes(function (): void {
        Route::group(['prefix' => 'newsletters', 'as' => 'newsletter.'], function (): void {
            Route::resource('', 'NewsletterController')->only(['index', 'destroy'])->parameters(['' => 'newsletter']);
        });

        Route::group(['prefix' => 'settings'], function (): void {
            Route::get('newsletter', [
                'as' => 'newsletter.settings',
                'uses' => 'Settings\NewsletterSettingController@edit',
            ]);

            Route::put('newsletter', [
                'as' => 'newsletter.settings.update',
                'uses' => 'Settings\NewsletterSettingController@update',
                'permission' => 'newsletter.settings',
            ]);
        });
    });

    if (defined('THEME_MODULE_SCREEN_NAME')) {
        Theme::registerRoutes(function (): void {
            Route::post('newsletter/subscribe', [
                'as' => 'public.newsletter.subscribe',
                'uses' => 'PublicController@postSubscribe',
            ]);

            Route::get('newsletter/unsubscribe/{user}', [
                'as' => 'public.newsletter.unsubscribe',
                'uses' => 'PublicController@getUnsubscribe',
            ]);

            Route::get('ajax/newsletter/popup', 'PublicController@ajaxLoadPopup')
                ->middleware(RequiresJsonRequestMiddleware::class)
                ->name('public.ajax.newsletter-popup');
        });
    }
});
