<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function (): void {
    Route::group(['namespace' => 'Botble\AuditLog\Http\Controllers'], function (): void {
        Route::group(['prefix' => 'audit-logs'], function (): void {
            Route::resource('', 'AuditLogController', ['names' => 'audit-log'])->only(['index', 'destroy']);

            Route::get('widgets/activities', [
                'as' => 'audit-log.widget.activities',
                'uses' => 'AuditLogController@getWidgetActivities',
                'permission' => 'audit-log.index',
            ]);

            Route::get('items/empty', [
                'as' => 'audit-log.empty',
                'uses' => 'AuditLogController@deleteAll',
                'permission' => 'audit-log.destroy',
            ]);
        });
    });
});
