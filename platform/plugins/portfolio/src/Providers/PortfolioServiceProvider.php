<?php

namespace Botble\Portfolio\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\Portfolio\Forms\Fronts\QuotationForm;
use Botble\Portfolio\Http\Requests\QuoteRequest;
use Botble\Portfolio\Models\CustomField;
use Botble\Portfolio\Models\CustomFieldOption;
use Botble\Portfolio\Models\Package;
use Botble\Portfolio\Models\Project;
use Botble\Portfolio\Models\Service;
use Botble\Portfolio\Models\ServiceCategory;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Events\ThemeRoutingBeforeEvent;
use Botble\Theme\Facades\SiteMapManager;
use Botble\Theme\FormFrontManager;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\RouteMatched;

class PortfolioServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/portfolio')
            ->loadAndPublishConfigurations(['permissions', 'email'])
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes()
            ->loadMigrations()
            ->publishAssets()
            ->registerSlugHelper()
            ->registerSeoHelper()
            ->registerLanguage();

        $this->app->register(EventServiceProvider::class);

        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem([
                    'id' => 'cms-core-portfolio',
                    'priority' => 10,
                    'name' => 'plugins/portfolio::portfolio.name',
                    'icon' => 'ti ti-briefcase',
                    'permissions' => ['portfolio.index'],
                ])
                ->registerItem([
                    'id' => 'cms-core-portfolio-projects',
                    'priority' => 1,
                    'parent_id' => 'cms-core-portfolio',
                    'name' => 'plugins/portfolio::portfolio.project.name',
                    'permissions' => ['portfolio.projects.index'],
                    'url' => route('portfolio.projects.index'),
                ])
                ->registerItem([
                    'id' => 'cms-core-portfolio-service-categories',
                    'priority' => 2,
                    'parent_id' => 'cms-core-portfolio',
                    'name' => 'plugins/portfolio::portfolio.service_category.name',
                    'permissions' => ['portfolio.service-categories.index'],
                    'url' => route('portfolio.service-categories.index'),
                ])
                ->registerItem([
                    'id' => 'cms-core-portfolio-services',
                    'priority' => 3,
                    'parent_id' => 'cms-core-portfolio',
                    'name' => 'plugins/portfolio::portfolio.service.name',
                    'permissions' => ['portfolio.services.index'],
                    'url' => route('portfolio.services.index'),
                ])
                ->registerItem([
                    'id' => 'cms-core-portfolio-packages',
                    'priority' => 4,
                    'parent_id' => 'cms-core-portfolio',
                    'name' => 'plugins/portfolio::portfolio.package.name',
                    'permissions' => ['portfolio.packages.index'],
                    'url' => route('portfolio.packages.index'),
                ])
                ->registerItem([
                    'id' => 'cms-core-portfolio-quotation-requests',
                    'priority' => 5,
                    'parent_id' => 'cms-core-portfolio',
                    'name' => 'plugins/portfolio::portfolio.quotation_request.name',
                    'url' => route('portfolio.quotation-requests.index'),
                    'permissions' => ['portfolio.quotation-requests.index'],
                ])
                ->registerItem([
                    'id' => 'cms-core-portfolio-custom-fields',
                    'priority' => 5,
                    'parent_id' => 'cms-core-portfolio',
                    'name' => 'plugins/portfolio::portfolio.custom_field.name',
                    'url' => route('portfolio.custom-fields.index'),
                    'permissions' => ['portfolio.custom-fields.index'],
                ]);
        });

        $this->app['events']->listen(RouteMatched::class, function (): void {
            EmailHandler::addTemplateSettings('portfolio', config('plugins.portfolio.email', []));
        });

        $this->app->booted(function (Application $app): void {
            $app->register(HookServiceProvider::class);
        });

        $this->app['events']->listen(ThemeRoutingBeforeEvent::class, function (): void {
            SiteMapManager::registerKey([
                'service-categories',
                'services',
                'projects',
                'packages',
            ]);
        });

        FormFrontManager::register(QuotationForm::class, QuoteRequest::class);
    }

    protected function registerSlugHelper(): self
    {
        SlugHelper::registering(function (): void {
            SlugHelper::registerModule(ServiceCategory::class, fn () => trans('plugins/portfolio::portfolio.service_categories'));
            SlugHelper::registerModule(Service::class, fn () => trans('plugins/portfolio::portfolio.services'));
            SlugHelper::registerModule(Package::class, fn () => trans('plugins/portfolio::portfolio.packages'));
            SlugHelper::registerModule(Project::class, fn () => trans('plugins/portfolio::portfolio.projects'));

            SlugHelper::setPrefix(ServiceCategory::class, 'service-categories');
            SlugHelper::setPrefix(Service::class, 'services');
            SlugHelper::setPrefix(Package::class, 'packages');
            SlugHelper::setPrefix(Project::class, 'projects');
        });

        return $this;
    }

    protected function registerSeoHelper(): self
    {
        SeoHelper::registerModule(ServiceCategory::class);
        SeoHelper::registerModule(Service::class);
        SeoHelper::registerModule(Package::class);
        SeoHelper::registerModule(Project::class);

        return $this;
    }

    protected function registerLanguage(): self
    {
        if (! defined('LANGUAGE_MODULE_SCREEN_NAME') || ! defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
            return $this;
        }

        LanguageAdvancedManager::registerModule(Project::class, [
            'name',
            'description',
            'content',
        ]);

        LanguageAdvancedManager::registerModule(ServiceCategory::class, [
            'name',
            'description',
        ]);

        LanguageAdvancedManager::registerModule(Service::class, [
            'name',
            'description',
            'content',
        ]);

        LanguageAdvancedManager::registerModule(Package::class, [
            'name',
            'description',
            'content',
            'price',
            'annual_price',
            'features',
            'action_label',
            'action_url',
        ]);

        LanguageAdvancedManager::registerModule(CustomField::class, [
            'name',
            'placeholder',
        ]);

        LanguageAdvancedManager::registerModule(CustomFieldOption::class, [
            'label',
            'value',
        ]);

        LanguageAdvancedManager::addTranslatableMetaBox('custom_fields_box');

        add_action(LANGUAGE_ADVANCED_ACTION_SAVED, function ($data, $request): void {
            if ($data instanceof CustomField) {
                $options = $request->input('options', []);

                if (! $options) {
                    return;
                }

                $newRequest = new Request();

                $newRequest->replace([
                    'language' => $request->input('language'),
                    'ref_lang' => $request->input('ref_lang'),
                ]);

                foreach ($options as $value) {
                    if (! isset($value['id'])) {
                        continue;
                    }

                    $option = CustomFieldOption::query()->find($value['id']);

                    $newRequest->merge([
                        'label' => $value['label'],
                        'value' => $value['value'],
                    ]);

                    if ($option) {
                        LanguageAdvancedManager::save($option, $newRequest);
                    }
                }
            }
        }, 1234, 2);

        return $this;
    }
}
