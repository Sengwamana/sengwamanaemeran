<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Language\Http\Controllers'], function (): void {
    AdminHelper::registerRoutes(function (): void {
        Route::group(['prefix' => 'settings/languages'], function (): void {
            Route::get('', [
                'as' => 'languages.index',
                'uses' => 'LanguageController@index',
            ]);

            Route::get('options', [
                'as' => 'settings.language',
                'uses' => 'LanguageController@index',
                'permission' => 'languages.index',
            ]);

            Route::post('store', [
                'as' => 'languages.store',
                'uses' => 'LanguageController@store',
                'permission' => 'languages.create',
                'middleware' => 'preventDemo',
            ]);

            Route::post('edit', [
                'as' => 'languages.edit',
                'uses' => 'LanguageController@update',
                'middleware' => 'preventDemo',
            ]);

            Route::delete('delete/{id}', [
                'as' => 'languages.destroy',
                'uses' => 'LanguageController@destroy',
                'middleware' => 'preventDemo',
            ])->wherePrimaryKey();

            Route::get('set-default', [
                'as' => 'languages.set.default',
                'uses' => 'LanguageController@getSetDefault',
                'permission' => 'languages.edit',
            ]);

            Route::get('get', [
                'as' => 'languages.get',
                'uses' => 'LanguageController@getLanguage',
                'permission' => 'languages.edit',
            ]);

            Route::post('edit-setting', [
                'as' => 'languages.settings',
                'uses' => 'Settings\LanguageSettingController@update',
                'permission' => 'languages.edit',
            ]);
        });
    });

    Route::group(['prefix' => 'languages'], function (): void {
        Route::post('change-item-language', [
            'as' => 'languages.change.item.language',
            'uses' => 'LanguageController@postChangeItemLanguage',
            'permission' => false,
        ]);

        Route::get('change-data-language/{locale}', [
            'as' => 'languages.change.data.language',
            'uses' => 'LanguageController@getChangeDataLanguage',
            'permission' => false,
        ]);
    });
});
