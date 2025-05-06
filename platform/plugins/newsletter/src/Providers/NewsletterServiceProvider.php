<?php

namespace Botble\Newsletter\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\DashboardMenuItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Newsletter\Contracts\Factory;
use Botble\Newsletter\Forms\Fronts\NewsletterForm;
use Botble\Newsletter\Http\Requests\NewsletterRequest;
use Botble\Newsletter\Models\Newsletter;
use Botble\Newsletter\NewsletterManager;
use Botble\Newsletter\Repositories\Eloquent\NewsletterRepository;
use Botble\Newsletter\Repositories\Interfaces\NewsletterInterface;
use Botble\Setting\PanelSections\SettingOthersPanelSection;
use Botble\Theme\FormFrontManager;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Routing\Events\RouteMatched;

class NewsletterServiceProvider extends ServiceProvider implements DeferrableProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->singleton(NewsletterInterface::class, function () {
            return new NewsletterRepository(new Newsletter());
        });

        $this->app->singleton(Factory::class, function ($app) {
            return new NewsletterManager($app);
        });
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/newsletter')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions', 'email'])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->publishAssets()
            ->loadAndPublishViews()
            ->loadMigrations();

        $this->app->register(EventServiceProvider::class);

        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-newsletter')
                        ->priority(430)
                        ->name('plugins/newsletter::newsletter.name')
                        ->icon('ti ti-mail')
                        ->route('newsletter.index')
                );
        });

        PanelSectionManager::default()->beforeRendering(function (): void {
            PanelSectionManager::registerItem(
                SettingOthersPanelSection::class,
                fn () => PanelSectionItem::make('newsletter')
                    ->setTitle(trans('plugins/newsletter::newsletter.settings.title'))
                    ->withIcon('ti ti-mail-cog')
                    ->withDescription(trans('plugins/newsletter::newsletter.settings.panel_description'))
                    ->withPriority(140)
                    ->withRoute('newsletter.settings')
            );
        });

        $this->app['events']->listen(RouteMatched::class, function (): void {
            EmailHandler::addTemplateSettings(NEWSLETTER_MODULE_SCREEN_NAME, config('plugins.newsletter.email', []));
        });

        FormFrontManager::register(NewsletterForm::class, NewsletterRequest::class);
    }

    public function provides(): array
    {
        return [Factory::class];
    }
}
