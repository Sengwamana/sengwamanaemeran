<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Base\Http\Controllers\CacheManagementController;
use Botble\Base\Http\Controllers\CoreIconController;
use Botble\Base\Http\Controllers\NotificationController;
use Botble\Base\Http\Controllers\SearchController;
use Botble\Base\Http\Controllers\SystemInformationController;
use Botble\Base\Http\Controllers\ToggleThemeModeController;
use Botble\Base\Http\Middleware\RequiresJsonRequestMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Base\Http\Controllers'], function (): void {
    AdminHelper::registerRoutes(function (): void {
        Route::group(['prefix' => 'system'], function (): void {
            Route::get('', [
                'as' => 'system.index',
                'uses' => 'SystemController@getIndex',
                'permission' => 'core.system',
            ]);
        });

        Route::group(['permission' => 'superuser'], function (): void {
            Route::prefix('system/info')->group(function (): void {
                Route::match(['GET', 'POST'], '/', [SystemInformationController::class, 'index'])
                    ->name('system.info');
                Route::get('get-addition-data', [SystemInformationController::class, 'getAdditionData'])
                    ->middleware(RequiresJsonRequestMiddleware::class)
                    ->name('system.info.get-addition-data');
            });

            Route::prefix('system/cache')->group(function (): void {
                Route::get('', [CacheManagementController::class, 'index'])->name('system.cache');
                Route::post('clear', [CacheManagementController::class, 'destroy'])
                    ->name('system.cache.clear')
                    ->middleware('preventDemo');
            });
        });

        Route::get('system/check-update', [
            'as' => 'system.check-update',
            'uses' => 'SystemController@getCheckUpdate',
            'permission' => 'superuser',
        ]);

        Route::get('system/updater', [
            'as' => 'system.updater',
            'uses' => 'SystemController@getUpdater',
            'permission' => 'superuser',
        ]);

        Route::post('system/updater', [
            'as' => 'system.updater.post',
            'uses' => 'SystemController@postUpdater',
            'permission' => 'superuser',
            'middleware' => 'preventDemo',
        ]);

        Route::get('system/cleanup', [
            'as' => 'system.cleanup',
            'uses' => 'SystemController@getCleanup',
            'permission' => 'superuser',
        ]);

        Route::post('system/cleanup', [
            'as' => 'system.cleanup.process',
            'uses' => 'SystemController@getCleanup',
            'permission' => 'superuser',
            'middleware' => 'preventDemo',
        ]);

        Route::post('system/debug-mode/turn-off', [
            'as' => 'system.debug-mode.turn-off',
            'uses' => 'DebugModeController@postTurnOff',
            'permission' => 'superuser',
            'middleware' => 'preventDemo',
        ]);

        Route::get('system/cronjob', [
            'as' => 'system.cronjob',
            'uses' => 'CronjobSettingController@index',
        ]);

        Route::group(['permission' => false], function (): void {
            Route::post('membership/authorize', [
                'as' => 'membership.authorize',
                'uses' => 'SystemController@postAuthorize',
            ]);

            Route::get('menu-items-count', [
                'as' => 'menu-items-count',
                'uses' => 'SystemController@getMenuItemsCount',
            ]);

            Route::get('unlicensed', [
                'as' => 'unlicensed',
                'uses' => 'UnlicensedController@index',
            ]);

            Route::post('unlicensed', [
                'as' => 'unlicensed.skip',
                'uses' => 'UnlicensedController@postSkip',
            ]);

            Route::group(
                ['prefix' => 'notifications', 'as' => 'notifications.', 'controller' => NotificationController::class],
                function (): void {
                    Route::get('/', [
                        'as' => 'index',
                        'uses' => 'index',
                    ]);

                    Route::delete('{id}', [
                        'as' => 'destroy',
                        'uses' => 'destroy',
                    ])->wherePrimaryKey();

                    Route::get('read-notification/{id}', [
                        'as' => 'read-notification',
                        'uses' => 'read',
                    ])->wherePrimaryKey();

                    Route::put('read-all-notification', [
                        'as' => 'read-all-notification',
                        'uses' => 'readAll',
                    ]);

                    Route::delete('destroy-all-notification', [
                        'as' => 'destroy-all-notification',
                        'uses' => 'deleteAll',
                    ]);

                    Route::get('count-unread', [
                        'as' => 'count-unread',
                        'uses' => 'countUnread',
                    ]);
                }
            );

            Route::get('toggle-theme-mode', [ToggleThemeModeController::class, '__invoke'])->name('toggle-theme-mode');

            Route::get('search', [SearchController::class, '__invoke'])->name('core.global-search');

            Route::get('core-icons', [CoreIconController::class, 'index'])
                ->name('core-icons')
                ->middleware(RequiresJsonRequestMiddleware::class);
        });
    });
});
