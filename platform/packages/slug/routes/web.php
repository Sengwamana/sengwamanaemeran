<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Slug\Http\Controllers'], function (): void {
    AdminHelper::registerRoutes(function (): void {
        Route::group(['prefix' => 'settings/permalink'], function (): void {
            Route::get('', [
                'as' => 'slug.settings',
                'uses' => 'SlugController@edit',
                'permission' => 'settings.options',
            ]);

            Route::put('', [
                'as' => 'slug.settings.update',
                'uses' => 'SlugController@update',
                'permission' => 'settings.options',
                'middleware' => 'preventDemo',
            ]);
        });
    });

    Route::group(['prefix' => 'ajax/slug', 'middleware' => ['web', 'core']], function (): void {
        Route::post('create', [
            'as' => 'slug.create',
            'uses' => 'SlugController@store',
        ]);
    });
});
