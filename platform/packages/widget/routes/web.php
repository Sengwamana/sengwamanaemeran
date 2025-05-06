<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Widget\Http\Controllers'], function (): void {
    AdminHelper::registerRoutes(function (): void {
        Route::group(['prefix' => 'widgets'], function (): void {
            Route::get('load-widget', 'WidgetController@showWidget');

            Route::get('', [
                'as' => 'widgets.index',
                'uses' => 'WidgetController@index',
            ]);

            Route::post('save-widgets-to-sidebar', [
                'as' => 'widgets.save_widgets_sidebar',
                'uses' => 'WidgetController@update',
                'permission' => 'widgets.index',
            ]);

            Route::delete('delete', [
                'as' => 'widgets.destroy',
                'uses' => 'WidgetController@destroy',
                'permission' => 'widgets.index',
            ]);
        });
    });
});
