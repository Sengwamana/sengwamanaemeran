<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Theme\Http\Controllers'], function (): void {
    AdminHelper::registerRoutes(function (): void {
        if (config('packages.theme.general.display_theme_manager_in_admin_panel', true)) {
            Route::group(['prefix' => 'theme'], function (): void {
                Route::get('all', [
                    'as' => 'theme.index',
                    'uses' => 'ThemeController@index',
                ]);

                Route::post('active', [
                    'as' => 'theme.active',
                    'uses' => 'ThemeController@postActivateTheme',
                    'middleware' => 'preventDemo',
                    'permission' => 'theme.index',
                ]);

                Route::post('remove', [
                    'as' => 'theme.remove',
                    'uses' => 'ThemeController@postRemoveTheme',
                    'middleware' => 'preventDemo',
                    'permission' => 'theme.index',
                ]);
            });
        }

        Route::group(['prefix' => 'theme/options/{id?}'], function (): void {
            Route::get('', [
                'as' => 'theme.options',
                'uses' => 'ThemeController@getOptions',
            ]);

            Route::post('', [
                'as' => 'theme.options.post',
                'uses' => 'ThemeController@postUpdate',
                'permission' => 'theme.options',
            ]);
        });

        Route::group(['prefix' => 'theme/custom-css'], function (): void {
            Route::get('', [
                'as' => 'theme.custom-css',
                'uses' => 'ThemeController@getCustomCss',
            ]);

            Route::post('', [
                'as' => 'theme.custom-css.post',
                'uses' => 'ThemeController@postCustomCss',
                'permission' => 'theme.custom-css',
                'middleware' => 'preventDemo',
            ]);
        });

        Route::group(['prefix' => 'theme/custom-js'], function (): void {
            Route::get('', [
                'as' => 'theme.custom-js',
                'uses' => 'ThemeController@getCustomJs',
            ]);

            Route::post('', [
                'as' => 'theme.custom-js.post',
                'uses' => 'ThemeController@postCustomJs',
                'permission' => 'theme.custom-js',
                'middleware' => 'preventDemo',
            ]);
        });

        Route::group(['prefix' => 'theme/custom-html'], function (): void {
            Route::get('', [
                'as' => 'theme.custom-html',
                'uses' => 'ThemeController@getCustomHtml',
            ]);

            Route::post('', [
                'as' => 'theme.custom-html.post',
                'uses' => 'ThemeController@postCustomHtml',
                'permission' => 'theme.custom-html',
                'middleware' => 'preventDemo',
            ]);
        });

        Route::group(['prefix' => 'theme/robots-txt'], function (): void {
            Route::get('', [
                'as' => 'theme.robots-txt',
                'uses' => 'ThemeController@getRobotsTxt',
            ]);

            Route::post('', [
                'as' => 'theme.robots-txt.post',
                'uses' => 'ThemeController@postRobotsTxt',
                'permission' => 'theme.robots-txt',
                'middleware' => 'preventDemo',
            ]);
        });

        Route::prefix('settings')->name('settings.')->group(function (): void {
            Route::prefix('website-tracking')->group(function (): void {
                Route::get('/', [
                    'as' => 'website-tracking',
                    'uses' => 'WebsiteTrackingSettingController@edit',
                ]);

                Route::put('/', [
                    'as' => 'website-tracking.update',
                    'uses' => 'WebsiteTrackingSettingController@update',
                    'permission' => 'settings.website-tracking',
                ]);
            });
        });
    });
});
