<?php

namespace Botble\Base\Providers;

use Botble\Base\Contracts\GlobalSearchableManager as GlobalSearchableManagerContract;
use Botble\Base\Contracts\PanelSections\Manager;
use Botble\Base\Exceptions\Handler;
use Botble\Base\Facades\AdminAppearance;
use Botble\Base\Facades\AdminHelper;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Breadcrumb as BreadcrumbFacade;
use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Facades\PanelSectionManager as PanelSectionManagerFacade;
use Botble\Base\GlobalSearch\GlobalSearchableManager;
use Botble\Base\Models\BaseModel;
use Botble\Base\PanelSections\Manager as PanelSectionManager;
use Botble\Base\PanelSections\System\SystemPanelSection;
use Botble\Base\Repositories\Eloquent\MetaBoxRepository;
use Botble\Base\Repositories\Interfaces\MetaBoxInterface;
use Botble\Base\Supports\Action;
use Botble\Base\Supports\Breadcrumb;
use Botble\Base\Supports\CustomResourceRegistrar;
use Botble\Base\Supports\DashboardMenuItem;
use Botble\Base\Supports\Database\Blueprint;
use Botble\Base\Supports\Filter;
use Botble\Base\Supports\GoogleFonts;
use Botble\Base\Supports\Helper;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Base\Widgets\AdminWidget;
use Botble\Base\Widgets\Contracts\AdminWidget as AdminWidgetContract;
use Botble\Setting\Providers\SettingServiceProvider;
use Botble\Setting\Supports\SettingStore;
use DateTimeZone;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Schema\Builder;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\ResourceRegistrar;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class BaseServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this
            ->setNamespace('core/base')
            ->loadAndPublishConfigurations('general')
            ->loadHelpers();

        $this->app->instance('core.middleware', []);

        config()->set(['session.cookie' => 'new_app_session']);

        $this->app->bind(ResourceRegistrar::class, function (Application $app) {
            return new CustomResourceRegistrar($app['router']);
        });

        $this->app->register(SettingServiceProvider::class);

        $this->app->singleton(ExceptionHandler::class, Handler::class);

        $this->app->singleton(Breadcrumb::class);

        $this->app->singleton(Manager::class, PanelSectionManager::class);

        $this->app->singleton(GlobalSearchableManagerContract::class, GlobalSearchableManager::class);

        $this->app->bind(MetaBoxInterface::class, MetaBoxRepository::class);

        $this->app->singleton('core.action', Action::class);

        $this->app->singleton('core.filter', Filter::class);

        $this->app->singleton(AdminWidgetContract::class, AdminWidget::class);

        $this->app->singleton('core.google-fonts', GoogleFonts::class);

        $this->registerRouteMacros();

        $this->prepareAliasesIfMissing();

        config()->set(['session.cookie' => Str::slug(config('core.base.general.session_cookie', 'botble_session'), '_')]);

        $this->overrideDefaultConfigs();

        Schema::defaultStringLength(191);
    }

    public function boot(): void
    {
        $this
            ->loadAndPublishConfigurations(['permissions', 'assets'])
            ->loadAndPublishViews()
            ->loadAnonymousComponents()
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadMigrations()
            ->publishAssets();

        $this->app['blade.compiler']->anonymousComponentPath($this->getViewsPath() . '/components', 'core');

        $this->overridePackagesConfigs();

        $this->app->booted(function (): void {
            do_action(BASE_ACTION_INIT);
        });

        $this->registerDashboardMenus();

        $this->registerPanelSections();

        Paginator::useBootstrap();

        $this->forceSSL();

        $this->configureIni();

        $this->app->extend('db.schema', function (Builder $schema) {
            $schema->blueprintResolver(function ($table, $callback, $prefix) {
                return new Blueprint($table, $callback, $prefix);
            });

            return $schema;
        });
    }

    protected function registerDashboardMenus(): void
    {
        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-core-system')
                        ->priority(10000)
                        ->name('core/base::layouts.platform_admin')
                        ->icon('ti ti-user-shield')
                        ->route('system.index')
                        ->permission('core.system')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-core-tools')
                        ->priority(9000)
                        ->name('core/base::layouts.tools')
                        ->icon('ti ti-tool')
                        ->permission('core.tools')
                );
        });
    }

    protected function registerPanelSections(): void
    {
        PanelSectionManagerFacade::group('system')->beforeRendering(function (): void {
            PanelSectionManagerFacade::setGroupName(trans('core/base::layouts.platform_admin'))
                ->register(SystemPanelSection::class);
        });
    }

    protected function configureIni(): void
    {
        $currentLimit = @ini_get('memory_limit');
        $currentLimitInt = Helper::convertHrToBytes($currentLimit);

        $baseConfig = $this->getBaseConfig();

        $memoryLimit = Arr::get($baseConfig, 'memory_limit');

        if (! $memoryLimit) {
            if (Helper::isIniValueChangeable('memory_limit') === false) {
                $memoryLimit = $currentLimit;
            } else {
                $memoryLimit = '256M';
            }
        }

        $limitInt = Helper::convertHrToBytes($memoryLimit);
        if ($currentLimitInt !== -1 && ($limitInt === -1 || $limitInt > $currentLimitInt)) {
            BaseHelper::iniSet('memory_limit', $memoryLimit);
        }

        $maxExecutionTime = Arr::get($baseConfig, 'max_execution_time');

        $currentExecutionTimeLimit = @ini_get('max_execution_time');

        if ($currentExecutionTimeLimit < $maxExecutionTime) {
            BaseHelper::iniSet('max_execution_time', $maxExecutionTime);
        }
    }

    protected function forceSSL(): void
    {
        $baseConfig = $this->getBaseConfig();

        $forceUrl = Arr::get($baseConfig, 'force_root_url');
        if (! empty($forceUrl)) {
            URL::forceRootUrl($forceUrl);
        }

        $forceSchema = Arr::get($baseConfig, 'force_schema');
        if (! empty($forceSchema)) {
            $this->app['request']->server->set('HTTPS', 'on');

            URL::forceScheme($forceSchema);
        }
    }

    protected function getBaseConfig(): array
    {
        return $this->getConfig()->get('core.base.general', []);
    }

    protected function getConfig(): Repository
    {
        return $this->app['config'];
    }

    protected function overrideDefaultConfigs(): void
    {
        $config = $this->getConfig();

        $config->set([
            'app.debug_blacklist' => [
                '_ENV' => [
                    'APP_KEY',
                    'ADMIN_DIR',
                    'DB_DATABASE',
                    'DB_USERNAME',
                    'DB_PASSWORD',
                    'REDIS_PASSWORD',
                    'MAIL_PASSWORD',
                    'PUSHER_APP_KEY',
                    'PUSHER_APP_SECRET',
                ],
                '_SERVER' => [
                    'APP_KEY',
                    'ADMIN_DIR',
                    'DB_DATABASE',
                    'DB_USERNAME',
                    'DB_PASSWORD',
                    'REDIS_PASSWORD',
                    'MAIL_PASSWORD',
                    'PUSHER_APP_KEY',
                    'PUSHER_APP_SECRET',
                ],
                '_POST' => [
                    'password',
                ],
            ],
            'debugbar.enabled' => $this->app->hasDebugModeEnabled() &&
                ! $this->app->runningInConsole() &&
                ! $this->app->environment(['testing', 'production']),
            'debugbar.capture_ajax' => false,
            'debugbar.remote_sites_path' => '',
            'core.base.general.google_fonts' => GoogleFonts::getFonts(),
        ]);

        if (
            ! $config->has('logging.channels.deprecations')
            && $this->app->isLocal()
            && $this->app->hasDebugModeEnabled()
        ) {
            $config->set([
                'logging.channels.deprecations' => [
                    'driver' => 'single',
                    'path' => storage_path('logs/php-deprecation-warnings.log'),
                ],
            ]);
        }
    }

    protected function overridePackagesConfigs(): void
    {
        $config = $this->getConfig();

        $baseConfig = $this->getBaseConfig();

        /**
         * @var SettingStore $setting
         */
        $setting = $this->app[SettingStore::class];
        $timezone = $setting->get('time_zone', $config->get('app.timezone'));
        $locale = $setting->get('locale', Arr::get($baseConfig, 'locale', $config->get('app.locale')));

        $this->app->setLocale($locale);

        if (in_array($timezone, DateTimeZone::listIdentifiers())) {
            date_default_timezone_set($timezone);
        }

        if ($iframeRegex = Arr::get($baseConfig, 'iframe_regex')) {
            $baseConfig['purifier']['default']['URI.SafeIframeRegexp'] = $iframeRegex;
        } else {
            $allowedIframeUrls = [
                'www.youtube.com/embed/',
                'player.vimeo.com/video/',
                'maps.google.com/maps',
                'www.google.com/maps|docs.google.com/',
                'drive.google.com/',
                'view.officeapps.live.com/op/embed.aspx',
                'onedrive.live.com/embed',
                'open.spotify.com/embed',
                parse_url($config->get('app.url'), PHP_URL_HOST),
            ];

            if ($extraUrl = Arr::get($baseConfig, 'allowed_iframe_urls', [])) {
                $allowedIframeUrls = [
                    ...$allowedIframeUrls,
                    ...explode('|', $extraUrl),
                ];
            }

            $allowedIframeUrls = implode('|', apply_filters('core_allowed_iframe_urls', $allowedIframeUrls));

            $baseConfig['purifier']['default']['URI.SafeIframeRegexp'] = '%^(http://|https://|//)(' . $allowedIframeUrls . ')%';
        }

        $config->set([
            'app.locale' => $locale,
            'app.timezone' => $timezone,
            'purifier.settings' => [
                ...$config->get('purifier.settings', []),
                ...Arr::get($baseConfig, 'purifier', []),
            ],
            'ziggy.except' => ['debugbar.*'],
            'datatables-buttons.pdf_generator' => 'excel',
            'excel.exports.csv.use_bom' => true,
            'dompdf.public_path' => public_path(),
            'database.connections.mysql.strict' => Arr::get($baseConfig, 'db_strict_mode'),
            'database.connections.mysql.prefix' => Arr::get($baseConfig, 'db_prefix'),
            'excel.imports.ignore_empty' => true,
            'excel.imports.csv.input_encoding' => Arr::get($baseConfig, 'csv_import_input_encoding', 'UTF-8'),
        ]);
    }

    protected function registerRouteMacros(): void
    {
        Route::macro('wherePrimaryKey', function (array|string|null $name = 'id') {
            if (! $name) {
                $name = 'id';
            }

            /**
             * @var Route $this
             */
            if (BaseModel::determineIfUsingUuidsForId()) {
                return $this->whereUuid($name);
            }

            if (BaseModel::determineIfUsingUlidsForId()) {
                return $this->whereUlid($name);
            }

            return $this->whereNumber($name);
        });

        Route::macro('permission', function (array|string|bool|null $value) {
            /**
             * @var Route $this
             */
            $action = $this->getAction();
            Arr::set($action, 'permission', $value);

            return $this->setAction($action);
        });
    }

    protected function prepareAliasesIfMissing(): void
    {
        $aliasLoader = AliasLoader::getInstance();

        if (! class_exists('BaseHelper')) {
            $aliasLoader->alias('BaseHelper', BaseHelper::class);
            $aliasLoader->alias('DashboardMenu', DashboardMenu::class);
            $aliasLoader->alias('PageTitle', PageTitle::class);
        }

        if (! class_exists('Breadcrumb')) {
            $aliasLoader->alias('Breadcrumb', BreadcrumbFacade::class);
        }

        if (! class_exists('PanelSectionManager')) {
            $aliasLoader->alias('PanelSectionManager', PanelSectionManagerFacade::class);
        }

        if (! class_exists('AdminAppearance')) {
            $aliasLoader->alias('AdminAppearance', AdminAppearance::class);
        }

        if (! class_exists('AdminHelper')) {
            $aliasLoader->alias('AdminHelper', AdminHelper::class);
        }
    }
}
