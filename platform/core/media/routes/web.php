<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Media\Http\Controllers'], function (): void {
    Route::get('media/files/{hash}/{id}', [
        'as' => 'media.indirect.url',
        'uses' => 'PublicMediaController@show',
        'middleware' => 'throttle',
    ]);

    AdminHelper::registerRoutes(function (): void {
        Route::group(['prefix' => 'media', 'as' => 'media.', 'permission' => 'media.index'], function (): void {
            Route::get('', [
                'as' => 'index',
                'uses' => 'MediaController@getMedia',
            ]);

            Route::get('popup', [
                'as' => 'popup',
                'uses' => 'MediaController@getPopup',
            ]);

            Route::get('list', [
                'as' => 'list',
                'uses' => 'MediaController@getList',
            ]);

            Route::get('breadcrumbs', [
                'as' => 'breadcrumbs',
                'uses' => 'MediaController@getBreadcrumbs',
            ]);

            Route::post('global-actions', [
                'as' => 'global_actions',
                'uses' => 'MediaController@postGlobalActions',
            ]);

            Route::post('download', [
                'as' => 'download',
                'uses' => 'MediaController@download',
            ]);

            Route::group(['prefix' => 'files'], function (): void {
                Route::post('upload', [
                    'as' => 'files.upload',
                    'uses' => 'MediaFileController@postUpload',
                ]);

                Route::post('upload-from-editor', [
                    'as' => 'files.upload.from.editor',
                    'uses' => 'MediaFileController@postUploadFromEditor',
                ]);

                Route::post('download-url', [
                    'as' => 'download_url',
                    'uses' => 'MediaFileController@postDownloadUrl',
                ]);
            });

            Route::group(['prefix' => 'folders'], function (): void {
                Route::post('create', [
                    'as' => 'folders.create',
                    'uses' => 'MediaFolderController@store',
                ]);
            });
        });
    });
});
