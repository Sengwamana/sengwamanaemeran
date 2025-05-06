<?php

namespace ArchiElite\Announcement\Providers;

use ArchiElite\Announcement\Models\Announcement;
use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\Setting\PanelSections\SettingOthersPanelSection;
use Illuminate\Foundation\Application;

class AnnouncementServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/announcement')
            ->loadRoutes()
            ->loadAndPublishConfigurations('permissions')
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadMigrations()
            ->publishAssets();

        $this->app->booted(fn (Application $app) => $app->register(HookServiceProvider::class));

        DashboardMenu::beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem([
                    'id' => 'cms-plugins-announcement',
                    'priority' => 10,
                    'parent_id' => null,
                    'name' => 'plugins/announcement::announcements.name',
                    'url' => route('announcements.index'),
                    'icon' => 'ti ti-speakerphone',
                    'permissions' => ['announcements.index'],
                ]);
        });

        PanelSectionManager::beforeRendering(function (): void {
            PanelSectionManager::default()
                ->registerItem(
                    SettingOthersPanelSection::class,
                    fn () => PanelSectionItem::make('announcement-settings')
                        ->setTitle(trans('plugins/announcement::announcements.settings.name'))
                        ->withIcon('ti ti-speakerphone')
                        ->withDescription(trans('plugins/announcement::announcements.settings.description'))
                        ->withPriority(999)
                        ->withRoute('announcements.settings')
                );
        });

        if (
            defined('LANGUAGE_MODULE_SCREEN_NAME')
            && defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')
        ) {
            LanguageAdvancedManager::registerModule(Announcement::class, ['content', 'action_label']);
        }
    }
}
