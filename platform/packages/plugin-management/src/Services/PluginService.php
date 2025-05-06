<?php

namespace Botble\PluginManagement\Services;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Services\ClearCacheService;
use Botble\Base\Supports\Helper;
use Botble\PluginManagement\Events\ActivatedPluginEvent;
use Botble\PluginManagement\Events\DeactivatedPlugin;
use Botble\PluginManagement\Events\RemovedPlugin;
use Botble\PluginManagement\PluginManifest;
use Botble\Setting\Facades\Setting;
use Composer\Autoload\ClassLoader;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class PluginService
{
    protected static array $activatedPlugins = [];

    public function __construct(
        protected Application $app,
        protected Filesystem $files,
        protected PluginManifest $pluginManifest
    ) {
    }

    public function activate(string $plugin): array
    {
        $validate = $this->validate($plugin);

        if ($validate['error']) {
            return $validate;
        }

        $content = $this->getPluginInfo($plugin);

        if (empty($content)) {
            return [
                'error' => true,
                'message' => trans('packages/plugin-management::plugin.invalid_json'),
            ];
        }

        $pluginName = Arr::get($content, 'name') ?? Str::studly($plugin);

        $minimumCoreVersion = Arr::get($content, 'minimum_core_version');
        $coreVersion = get_core_version();

        if ($minimumCoreVersion && (version_compare($coreVersion, $minimumCoreVersion, '<'))) {
            return [
                'error' => true,
                'message' => trans('packages/plugin-management::plugin.minimum_core_version_not_met', [
                    'plugin' => $pluginName,
                    'minimum_core_version' => $minimumCoreVersion,
                    'current_core_version' => $coreVersion,
                ]),
            ];
        }

        if (! Arr::get($content, 'ready', 1)) {
            return [
                'error' => true,
                'message' => trans(
                    'packages/plugin-management::plugin.plugin_is_not_ready',
                    ['name' => $pluginName]
                ),
            ];
        }

        $this->clearCache();

        $activatedPlugins = get_active_plugins();
        if (! in_array($plugin, $activatedPlugins)) {
            $requiredPlugins = $this->getDependencies($plugin);

            if ($missingPlugins = array_diff($requiredPlugins, $activatedPlugins)) {
                return [
                    'error' => true,
                    'message' => trans(
                        'packages/plugin-management::plugin.missing_required_plugins',
                        ['plugins' => implode(',', $missingPlugins)]
                    ),
                ];
            }

            if (! class_exists($content['provider'])) {
                $loader = new ClassLoader();
                $loader->setPsr4($content['namespace'], plugin_path($plugin . '/src'));
                $loader->register(true);

                $this->app->register($content['provider']);

                if (class_exists($content['namespace'] . 'Plugin')) {
                    call_user_func([$content['namespace'] . 'Plugin', 'activate']);
                }

                $this->runMigrations($plugin);

                $published = $this->publishAssets($plugin);

                if ($published['error']) {
                    return $published;
                }

                $this->publishTranslations($plugin);
            }

            $activatedPlugins = array_merge($activatedPlugins, [$plugin]);

            $this->saveActivatedPlugins($activatedPlugins);

            if (class_exists($content['namespace'] . 'Plugin')) {
                call_user_func([$content['namespace'] . 'Plugin', 'activated']);
            }

            $this->clearCache();

            $this->pluginManifest->generateManifest();

            event(new ActivatedPluginEvent($plugin));

            return [
                'error' => false,
                'message' => trans('packages/plugin-management::plugin.activate_success'),
            ];
        }

        return [
            'error' => true,
            'message' => trans('packages/plugin-management::plugin.activated_already'),
        ];
    }

    protected function validate(string $plugin): array
    {
        $location = plugin_path($plugin);

        if (! $this->files->isDirectory($location)) {
            return [
                'error' => true,
                'message' => trans('packages/plugin-management::plugin.plugin_not_exist'),
            ];
        }

        if (! $this->getPluginInfo($plugin)) {
            return [
                'error' => true,
                'message' => trans('packages/plugin-management::plugin.missing_json_file'),
            ];
        }

        if ($this->isInBlacklist($plugin)) {
            return [
                'error' => true,
                'message' => trans('packages/plugin-management::plugin.plugin_invalid'),
            ];
        }

        return [
            'error' => false,
        ];
    }

    public function publishTranslations(string $plugin): void
    {
        if ($this->files->isDirectory(plugin_path($plugin . '/resources/lang'))) {
            $publishedPath = lang_path('vendor') . '/' . $this->getPluginNamespace($plugin);
            $this->files->copyDirectory(plugin_path($plugin . '/resources/lang'), $publishedPath);
        }
    }

    public function publishAssets(string $plugin): array
    {
        $validate = $this->validate($plugin);

        if ($validate['error']) {
            return $validate;
        }

        $pluginPath = public_path('vendor/core/plugins');

        if (! $this->files->isDirectory($pluginPath)) {
            $this->files->makeDirectory($pluginPath, 0755, true);
        }

        if (! $this->files->isWritable($pluginPath)) {
            return [
                'error' => true,
                'message' => trans(
                    'packages/plugin-management::plugin.folder_is_not_writeable',
                    ['name' => $pluginPath]
                ),
            ];
        }

        $publishedPath = public_path('vendor/core/' . $this->getPluginNamespace($plugin));

        $this->files->ensureDirectoryExists($publishedPath);

        if ($this->files->isDirectory(plugin_path($plugin . '/public'))) {
            $this->files->copyDirectory(plugin_path($plugin . '/public'), $publishedPath);
        }

        if ($this->files->exists(plugin_path($plugin . '/screenshot.png'))) {
            $this->files->copy(plugin_path($plugin . '/screenshot.png'), $publishedPath . '/screenshot.png');
        }

        return [
            'error' => false,
            'message' => trans('packages/plugin-management::plugin.published_assets_success', ['name' => $plugin]),
        ];
    }

    public function remove(string $plugin): array
    {
        $validate = $this->validate($plugin);

        if ($validate['error']) {
            return $validate;
        }

        $this->clearCache();

        $this->deactivate($plugin);

        $content = $this->getPluginInfo($plugin);

        if (! empty($content)) {
            if (! class_exists($content['provider'])) {
                $loader = new ClassLoader();
                $loader->setPsr4($content['namespace'], plugin_path($plugin . '/src'));
                $loader->register(true);
            }

            Schema::disableForeignKeyConstraints();

            try {
                if (class_exists($content['namespace'] . 'Plugin')) {
                    call_user_func([$content['namespace'] . 'Plugin', 'remove']);
                }
            } catch (Throwable $exception) {
                BaseHelper::logError($exception);
            }

            Schema::enableForeignKeyConstraints();
        }

        $location = plugin_path($plugin);

        $migrations = [];
        foreach (BaseHelper::scanFolder($location . '/database/migrations') as $file) {
            $migrations[] = pathinfo($file, PATHINFO_FILENAME);
        }

        DB::table('migrations')->whereIn('migration', $migrations)->delete();

        $this->files->deleteDirectory($location);

        if (empty($this->files->directories(plugin_path()))) {
            $this->files->deleteDirectory(plugin_path());
        }

        Helper::removeModuleFiles(Str::afterLast($this->getPluginNamespace($plugin), '/'), 'plugins');

        if (class_exists($content['namespace'] . 'Plugin')) {
            call_user_func([$content['namespace'] . 'Plugin', 'removed']);
        }

        $this->clearCache();

        $this->pluginManifest->generateManifest();

        RemovedPlugin::dispatch($plugin);

        return [
            'error' => false,
            'message' => trans('packages/plugin-management::plugin.plugin_removed'),
        ];
    }

    public function deactivate(string $plugin): array
    {
        $validate = $this->validate($plugin);

        if ($validate['error']) {
            return $validate;
        }

        $activatedPlugins = get_active_plugins();
        $content = $this->getPluginInfo($plugin);

        $requiredBy = [];

        foreach ($activatedPlugins as $activePlugin) {
            $pluginInfo = $this->getPluginInfo($activePlugin);

            if ($pluginInfo && isset($pluginInfo['required_plugins']) && in_array($plugin, $pluginInfo['required_plugins'])) {
                $requiredBy[$activePlugin] = $pluginInfo['name'];
            }
        }

        if (! empty($requiredBy)) {
            return [
                'error' => true,
                'message' => trans(
                    'packages/plugin-management::plugin.required_by_other_plugins',
                    ['plugin' => $content['name'], 'required_by' => implode(',', $requiredBy)]
                ),
            ];
        }

        $this->clearCache();

        if (! class_exists($content['provider'])) {
            $loader = new ClassLoader();
            $loader->setPsr4($content['namespace'], plugin_path($plugin . '/src'));
            $loader->register(true);
        }

        if (in_array($plugin, $activatedPlugins)) {
            if (class_exists($content['namespace'] . 'Plugin')) {
                call_user_func([$content['namespace'] . 'Plugin', 'deactivate']);
            }

            if (($key = array_search($plugin, $activatedPlugins)) !== false) {
                unset($activatedPlugins[$key]);
            }

            $this->saveActivatedPlugins($activatedPlugins);

            if (class_exists($content['namespace'] . 'Plugin')) {
                call_user_func([$content['namespace'] . 'Plugin', 'deactivated']);
            }

            $this->clearCache();

            $this->pluginManifest->generateManifest();

            DeactivatedPlugin::dispatch($plugin);

            return [
                'error' => false,
                'message' => trans('packages/plugin-management::plugin.deactivated_success'),
            ];
        }

        return [
            'error' => true,
            'message' => trans('packages/plugin-management::plugin.deactivated_already'),
        ];
    }

    public function getPluginNamespace(string $plugin): string
    {
        return $this->app['config']->get('core.base.general.plugin_namespaces.' . $plugin, $plugin);
    }

    protected function saveActivatedPlugins(array $plugins): array
    {
        $plugins = array_values($plugins);

        $availablePlugins = BaseHelper::scanFolder(plugin_path());

        $plugins = array_intersect($plugins, $availablePlugins);

        Setting::forceSet('activated_plugins', json_encode($plugins))->save();

        return $plugins;
    }

    public function clearCache(): void
    {
        Helper::clearCache();
        $cacheService = ClearCacheService::make();

        Cache::forget('core_installed_plugins');

        self::$activatedPlugins = [];

        $cacheService->clearConfig();
        $cacheService->clearRoutesCache();
    }

    public function runMigrations(string $plugin): void
    {
        $migrationPath = plugin_path($plugin . '/database/migrations');

        if (! $this->files->isDirectory($migrationPath)) {
            return;
        }

        $this->app['migrator']->run($migrationPath);
    }

    public function getDependencies(string $plugin): array
    {
        $plugin = strtolower($plugin);

        $content = $this->getPluginInfo($plugin);
        $requiredPlugins = $content['require'] ?? [];

        $activatedPlugins = get_active_plugins();

        foreach ($requiredPlugins as $key => $requiredPlugin) {
            if (in_array(Arr::last(explode('/', $requiredPlugin)), $activatedPlugins)) {
                unset($requiredPlugins[$key]);
            }
        }

        return $requiredPlugins;
    }

    public function getPluginInfo(string $plugin): array
    {
        $jsonFilePath = plugin_path($plugin . '/plugin.json');

        if (! $this->files->exists($jsonFilePath)) {
            return [];
        }

        return BaseHelper::getFileData($jsonFilePath);
    }

    public function validatePlugin(string $plugin, bool $throw = false): bool
    {
        $content = $this->getPluginInfo($plugin);

        $rules = [
            'id' => ['nullable', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:100'],
            'namespace' => ['required', 'string', 'max:200'],
            'provider' => ['required', 'string', 'max:250'],
            'author' => ['nullable', 'string', 'max:120'],
            'url' => ['nullable', 'string', 'url', 'max:255'],
            'version' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:400'],
            'require' => ['nullable', 'array', 'max:10'],
            'minimum_core_version' => ['nullable', 'string', 'regex:/^[0-9]+\.[0-9]+\.[0-9]+$/'],
        ];

        if ($throw) {
            $rules['id'] = ['required', 'string', 'max:100'];
        }

        $validator = Validator::make($content, $rules);

        $passes = $validator->passes();

        if (! $passes) {
            logger()->info($validator->getMessageBag()->toJson());

            if ($throw) {
                throw new Exception($validator->getMessageBag()->toJson());
            }
        }

        return $passes;
    }

    public function getInstalledPluginIds(): array
    {
        $installedPlugins = [];
        $plugins = BaseHelper::scanFolder(plugin_path());

        if (! empty($plugins)) {
            foreach ($plugins as $plugin) {
                $getInfoPlugin = $this->getPluginInfo($plugin);

                if (! empty($getInfoPlugin['id'])) {
                    $installedPlugins[$getInfoPlugin['id']] = $getInfoPlugin['version'];
                }
            }
        }

        return $installedPlugins;
    }

    protected function isInBlacklist(string $plugin): bool
    {
        $blacklist = [
            'activator',
            'botble-activator',
            'botble-activator-main',
            'shaqi/botble-activator',
        ];

        if (in_array($plugin, $blacklist)) {
            return true;
        }

        $pluginInfo = $this->getPluginInfo($plugin);

        if ($pluginInfo && isset($pluginInfo['id']) && in_array($pluginInfo['id'], $blacklist)) {
            return true;
        }

        return false;
    }

    public static function getActivatedPlugins(): array
    {
        if (self::$activatedPlugins && ! app()->runningInConsole()) {
            return self::$activatedPlugins;
        }

        if (
            Cache::has($key = 'core_installed_plugins')
            && ! app()->runningInConsole()
            && ($activatedPlugins = Cache::get($key))
        ) {
            self::$activatedPlugins = $activatedPlugins;

            return $activatedPlugins;
        }

        $activatedPlugins = Setting::get('activated_plugins');

        if (! $activatedPlugins) {
            return [];
        }

        $activatedPlugins = json_decode($activatedPlugins, true);

        if (! $activatedPlugins) {
            return [];
        }

        $plugins = array_unique($activatedPlugins);

        $existingPlugins = BaseHelper::scanFolder(plugin_path());

        $activatedPlugins = array_diff($plugins, array_diff($plugins, $existingPlugins));

        $activatedPlugins = array_values($activatedPlugins);

        Cache::forever('core_installed_plugins', $activatedPlugins);

        self::$activatedPlugins = $activatedPlugins;

        return $activatedPlugins;
    }

    public static function getInstalledPlugins(): array
    {
        $list = [];

        $plugins = BaseHelper::scanFolder(plugin_path());

        if (! empty($plugins)) {
            foreach ($plugins as $plugin) {
                $path = plugin_path($plugin);
                if (! File::isDirectory($path) || ! File::exists($path . '/plugin.json')) {
                    continue;
                }

                $list[] = $plugin;
            }
        }

        return $list;
    }
}
