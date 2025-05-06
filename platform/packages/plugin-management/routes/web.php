<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\PluginManagement\Http\Controllers'], function (): void {
    AdminHelper::registerRoutes(function (): void {
        Route::group(['prefix' => 'plugins'], function (): void {

            if (config('packages.plugin-management.general.enable_plugin_manager', true)) {
                Route::redirect('', 'plugins/installed');
                Route::get('installed', [
                    'as' => 'plugins.index',
                    'uses' => 'PluginManagementController@index',
                ]);

                Route::put('status', [
                    'as' => 'plugins.change.status',
                    'uses' => 'PluginManagementController@update',
                    'middleware' => 'preventDemo',
                    'permission' => 'plugins.index',
                ]);

                Route::delete('{plugin}', [
                    'as' => 'plugins.remove',
                    'uses' => 'PluginManagementController@destroy',
                    'middleware' => 'preventDemo',
                    'permission' => 'plugins.index',
                ]);

                Route::post('check-requirement', [
                    'as' => 'plugins.check-requirement',
                    'uses' => 'PluginManagementController@checkRequirement',
                    'permission' => 'plugins.index',
                ]);
            }

            if (config('packages.plugin-management.general.enable_marketplace_feature', true)) {
                Route::get('new', [
                    'as' => 'plugins.new',
                    'uses' => 'MarketplaceController@index',
                    'permission' => 'plugins.marketplace',
                ]);

                Route::group([
                    'prefix' => 'marketplace/ajax',
                    'permission' => 'plugins.marketplace',
                    'as' => 'plugins.marketplace.ajax.',
                ], function (): void {
                    Route::get('plugins', [
                        'as' => 'list',
                        'uses' => 'MarketplaceController@list',
                    ]);

                    Route::get('{id}', [
                        'as' => 'detail',
                        'uses' => 'MarketplaceController@detail',
                    ]);

                    Route::get('{id}/iframe', [
                        'as' => 'iframe',
                        'uses' => 'MarketplaceController@iframe',
                    ]);

                    Route::post('{id}/install', [
                        'as' => 'install',
                        'uses' => 'MarketplaceController@install',
                        'middleware' => 'preventDemo',
                    ]);

                    Route::post('{id}/update/{name?}', [
                        'as' => 'update',
                        'uses' => 'MarketplaceController@update',
                        'middleware' => 'preventDemo',
                    ]);

                    Route::post('check-update', [
                        'as' => 'check-update',
                        'uses' => 'MarketplaceController@checkUpdate',
                    ]);
                });
            }
        });
    });
});
