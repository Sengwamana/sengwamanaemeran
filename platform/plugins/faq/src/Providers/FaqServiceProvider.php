<?php

namespace Botble\Faq\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\DashboardMenuItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Faq\Contracts\Faq as FaqContract;
use Botble\Faq\FaqSupport;
use Botble\Faq\Models\Faq;
use Botble\Faq\Models\FaqCategory;
use Botble\Faq\Repositories\Eloquent\FaqCategoryRepository;
use Botble\Faq\Repositories\Eloquent\FaqRepository;
use Botble\Faq\Repositories\Interfaces\FaqCategoryInterface;
use Botble\Faq\Repositories\Interfaces\FaqInterface;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\Setting\PanelSections\SettingOthersPanelSection;

class FaqServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(FaqCategoryInterface::class, function () {
            return new FaqCategoryRepository(new FaqCategory());
        });

        $this->app->bind(FaqInterface::class, function () {
            return new FaqRepository(new Faq());
        });

        $this->app->singleton(FaqContract::class, FaqSupport::class);
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/faq')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions', 'general'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->publishAssets();

        if (defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
            LanguageAdvancedManager::registerModule(Faq::class, [
                'question',
                'answer',
            ]);

            LanguageAdvancedManager::registerModule(FaqCategory::class, [
                'name',
            ]);

            LanguageAdvancedManager::addTranslatableMetaBox('faq_schema_config_wrapper');

            foreach (config('plugins.faq.general.schema_supported', []) as $item) {
                $translatableColumns = array_merge(LanguageAdvancedManager::getTranslatableColumns($item), ['faq_schema_config', 'faq_ids']);

                LanguageAdvancedManager::registerModule($item, $translatableColumns);
            }
        }

        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-faq')
                        ->priority(420)
                        ->name('plugins/faq::faq.name')
                        ->icon('ti ti-help-octagon')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-faq-list')
                        ->priority(0)
                        ->parentId('cms-plugins-faq')
                        ->name('plugins/faq::faq.name')
                        ->icon('ti ti-list-check')
                        ->route('faq.index')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-faq-category')
                        ->priority(10)
                        ->parentId('cms-plugins-faq')
                        ->name('plugins/faq::faq-category.name')
                        ->icon('ti ti-folder')
                        ->route('faq_category.index')
                );
        });

        PanelSectionManager::default()->beforeRendering(function (): void {
            PanelSectionManager::registerItem(
                SettingOthersPanelSection::class,
                fn () => PanelSectionItem::make('faqs')
                    ->setTitle(trans('plugins/faq::faq.settings.title'))
                    ->withIcon('ti ti-settings-question')
                    ->withPriority(420)
                    ->withDescription(trans('plugins/faq::faq.settings.description'))
                    ->withRoute('faqs.settings')
            );
        });

        $this->app->register(HookServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
    }
}
