<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Optimize\Http\Controllers'], function (): void {
    AdminHelper::registerRoutes(function (): void {
        Route::group(['prefix' => 'settings'], function (): void {
            Route::get('optimize', [
                'as' => 'optimize.settings',
                'uses' => 'Settings\OptimizeSettingController@edit',
            ]);

            Route::put('optimize', [
                'as' => 'optimize.settings.update',
                'uses' => 'Settings\OptimizeSettingController@update',
                'permission' => 'optimize.settings',
            ]);
        });
    });
});
