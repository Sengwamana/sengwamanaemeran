<?php

use Botble\Analytics\Http\Controllers\AnalyticsController;
use Botble\Analytics\Http\Controllers\AnalyticsSettingJsonController;
use Botble\Analytics\Http\Controllers\Settings\AnalyticsSettingController;
use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function (): void {
    Route::group(['prefix' => 'analytics', 'as' => 'analytics.'], function (): void {
        Route::controller(AnalyticsController::class)->group(function (): void {
            Route::get('general', [
                'as' => 'general',
                'uses' => 'getGeneral',
            ]);

            Route::get('page', [
                'as' => 'page',
                'uses' => 'getTopVisitPages',
            ]);

            Route::get('browser', [
                'as' => 'browser',
                'uses' => 'getTopBrowser',
            ]);

            Route::get('referrer', [
                'as' => 'referrer',
                'uses' => 'getTopReferrer',
            ]);
        });
    });

    Route::group([
        'prefix' => 'settings/analytics',
        'as' => 'analytics.settings',
        'permission' => 'analytics.settings',
    ], function (): void {
        Route::get('/', [
            'uses' => AnalyticsSettingController::class . '@edit',
        ]);

        Route::put('/', [
            'as' => '.update',
            'uses' => AnalyticsSettingController::class . '@update',
        ]);

        Route::post('json', [
            'as' => '.json',
            'uses' => AnalyticsSettingJsonController::class . '@__invoke',
        ]);
    });
});
