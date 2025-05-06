<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Faq\Http\Controllers'], function (): void {
    AdminHelper::registerRoutes(function (): void {
        Route::group(['prefix' => 'faq-categories', 'as' => 'faq_category.'], function (): void {
            Route::resource('', 'FaqCategoryController')->parameters(['' => 'faq_category']);
        });

        Route::group(['prefix' => 'faqs', 'as' => 'faq.'], function (): void {
            Route::resource('', 'FaqController')->parameters(['' => 'faq']);
        });

        Route::group(['prefix' => 'settings'], function (): void {
            Route::get('faqs', [
                'as' => 'faqs.settings',
                'uses' => 'Settings\FaqSettingController@edit',
            ]);

            Route::put('faqs', [
                'as' => 'faqs.settings.update',
                'uses' => 'Settings\FaqSettingController@update',
                'permission' => 'faqs.settings',
            ]);
        });
    });
});
