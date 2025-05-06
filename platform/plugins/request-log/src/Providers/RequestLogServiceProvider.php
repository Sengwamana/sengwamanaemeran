<?php

namespace Botble\RequestLog\Providers;

use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\PanelSections\System\SystemPanelSection;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\RequestLog\Models\RequestLog as RequestLogModel;
use Botble\RequestLog\Repositories\Eloquent\RequestLogRepository;
use Botble\RequestLog\Repositories\Interfaces\RequestLogInterface;

/**
 * @since 02/07/2016 09:50 AM
 */
class RequestLogServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(RequestLogInterface::class, function () {
            return new RequestLogRepository(new RequestLogModel());
        });
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/request-log')
            ->loadHelpers()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->publishAssets();

        PanelSectionManager::group('system')->beforeRendering(function (): void {
            PanelSectionManager::registerItem(
                SystemPanelSection::class,
                fn () => PanelSectionItem::make('request-logs')
                    ->setTitle(trans('plugins/request-log::request-log.name'))
                    ->withDescription(trans('plugins/request-log::request-log.description'))
                    ->withIcon('ti ti-note')
                    ->withPriority(10)
                    ->withRoute('request-log.index')
            );
        });

        $this->app->register(EventServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);

        $this->app->booted(function (): void {
            $this->app->register(HookServiceProvider::class);
        });
    }
}
