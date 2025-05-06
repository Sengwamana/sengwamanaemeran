<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Portfolio\Http\Controllers\CustomFieldController;
use Botble\Portfolio\Http\Controllers\PackageController;
use Botble\Portfolio\Http\Controllers\ProjectController;
use Botble\Portfolio\Http\Controllers\PublicController;
use Botble\Portfolio\Http\Controllers\QuotationRequestController;
use Botble\Portfolio\Http\Controllers\ServiceCategoryController;
use Botble\Portfolio\Http\Controllers\ServiceController;
use Botble\Portfolio\Models\Package;
use Botble\Portfolio\Models\Project;
use Botble\Portfolio\Models\Service;
use Botble\Portfolio\Models\ServiceCategory;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function (): void {
    Route::prefix('portfolio')->name('portfolio.')->group(function (): void {
        Route::group(['prefix' => 'service-categories', 'as' => 'service-categories.'], function (): void {
            Route::resource('', ServiceCategoryController::class)->parameters(['' => 'service-category']);
        });

        Route::group(['prefix' => 'services', 'as' => 'services.'], function (): void {
            Route::resource('', ServiceController::class)->parameters(['' => 'service']);
        });

        Route::group(['prefix' => 'packages', 'as' => 'packages.'], function (): void {
            Route::resource('', PackageController::class)->parameters(['' => 'package']);
        });

        Route::group(['prefix' => 'projects', 'as' => 'projects.'], function (): void {
            Route::resource('', ProjectController::class)->parameters(['' => 'project']);
        });

        Route::group(['prefix' => 'custom-fields', 'as' => 'custom-fields.'], function (): void {
            Route::resource('', CustomFieldController::class)->parameters(['' => 'custom-field']);
        });

        Route::group(['prefix' => 'quotation-requests', 'as' => 'quotation-requests.'], function (): void {
            Route::resource('', QuotationRequestController::class)->parameters(['' => 'quotation-request'])->only(['index', 'edit', 'update', 'destroy']);
        });
    });
});

Theme::registerRoutes(function (): void {
    Route::get(
        sprintf('%s/{slug}', SlugHelper::getPrefix(ServiceCategory::class, 'service-categories')),
        [PublicController::class, 'category']
    )->name('public.category');

    Route::get(
        sprintf('%s/{slug}', SlugHelper::getPrefix(Service::class, 'services')),
        [PublicController::class, 'service']
    )->name('public.service');

    Route::get(
        sprintf('%s/{slug}', SlugHelper::getPrefix(Package::class, 'packages')),
        [PublicController::class, 'package']
    )->name('public.package');

    Route::get(
        sprintf('%s/{slug}', SlugHelper::getPrefix(Project::class, 'projects')),
        [PublicController::class, 'project']
    )->name('public.project');

    Route::prefix('portfolio')->name('portfolio.')->group(function (): void {
        Route::post('request-quote', [PublicController::class, 'storeQuote'])->name('request-quote');
    });
});
