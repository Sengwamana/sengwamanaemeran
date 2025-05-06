<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Translation\Http\Controllers\ExportOtherTranslationController;
use Botble\Translation\Http\Controllers\ExportThemeTranslationController;
use Botble\Translation\Http\Controllers\ImportOtherTranslationController;
use Botble\Translation\Http\Controllers\ImportThemeTranslationController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Translation\Http\Controllers'], function (): void {
    AdminHelper::registerRoutes(function (): void {
        Route::group(['prefix' => 'translations'], function (): void {
            Route::group(['prefix' => 'locales', 'permission' => 'translations.locales'], function (): void {
                Route::get('', [
                    'as' => 'translations.locales',
                    'uses' => 'LocaleController@index',
                ]);

                Route::post('', [
                    'as' => 'translations.locales.post',
                    'uses' => 'LocaleController@store',
                    'middleware' => 'preventDemo',
                ]);

                Route::delete('{locale}', [
                    'as' => 'translations.locales.delete',
                    'uses' => 'LocaleController@destroy',
                    'middleware' => 'preventDemo',
                ]);

                Route::get('download/{locale}', [
                    'as' => 'translations.locales.download',
                    'uses' => 'LocaleController@download',
                    'middleware' => 'preventDemo',
                ]);
            });

            Route::group(['permission' => 'translations.index'], function (): void {
                Route::match(['GET', 'POST'], '', [
                    'as' => 'translations.index',
                    'uses' => 'TranslationController@index',
                ]);

                Route::post('edit', [
                    'as' => 'translations.group.edit',
                    'uses' => 'TranslationController@update',
                    'middleware' => 'preventDemo',
                ]);
            });

            Route::group(['prefix' => 'theme', 'permission' => 'translations.theme-translations'], function (): void {
                Route::match(['GET', 'POST'], '', [
                    'as' => 'translations.theme-translations',
                    'uses' => 'ThemeTranslationController@index',
                ]);

                Route::post('post', [
                    'as' => 'translations.theme-translations.post',
                    'uses' => 'ThemeTranslationController@update',
                    'middleware' => 'preventDemo',
                ]);

                Route::post('re-import', [
                    'as' => 'translations.theme-translations.re-import',
                    'uses' => 'ReImportThemeTranslationController@__invoke',
                    'middleware' => 'preventDemo',
                ]);
            });
        });

        Route::prefix('tools/data-synchronize')->name('tools.data-synchronize.')->group(function (): void {
            Route::prefix('export')->name('export.')->group(function (): void {
                Route::group(['prefix' => 'theme-translations/export', 'as' => 'theme-translations.', 'permission' => 'theme-translations.export'], function (): void {
                    Route::get('/', [ExportThemeTranslationController::class, 'index'])->name('index');
                    Route::post('/', [ExportThemeTranslationController::class, 'store'])->name('store');
                });

                Route::group(['prefix' => 'other-translations', 'as' => 'other-translations.', 'permission' => 'other-translations.export'], function (): void {
                    Route::get('/', [ExportOtherTranslationController::class, 'index'])->name('index');
                    Route::post('/', [ExportOtherTranslationController::class, 'store'])->name('store');
                });
            });

            Route::prefix('import')->name('import.')->group(function (): void {
                Route::group(['prefix' => 'theme-translations', 'as' => 'theme-translations.', 'permission' => 'theme-translations.import'], function (): void {
                    Route::get('/', [ImportThemeTranslationController::class, 'index'])->name('index');
                    Route::post('/', [ImportThemeTranslationController::class, 'import'])->name('store');
                    Route::post('validate', [ImportThemeTranslationController::class, 'validateData'])->name('validate');
                });

                Route::group(['prefix' => 'other-translations', 'as' => 'other-translations.', 'permission' => 'other-translations.import'], function (): void {
                    Route::get('/', [ImportOtherTranslationController::class, 'index'])->name('index');
                    Route::post('/', [ImportOtherTranslationController::class, 'import'])->name('store');
                    Route::post('validate', [ImportOtherTranslationController::class, 'validateData'])->name('validate');
                });
            });
        });
    });
});
