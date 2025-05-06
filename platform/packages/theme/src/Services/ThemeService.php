<?php

namespace Botble\Theme\Services;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Helper;
use Botble\PluginManagement\Services\PluginService;
use Botble\Setting\Models\Setting;
use Botble\Setting\Supports\SettingStore;
use Botble\Theme\Events\ThemeRemoveEvent;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Facades\ThemeOption;
use Botble\Widget\Models\Widget;
use Carbon\Carbon;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ThemeService
{
    public function __construct(
        protected Filesystem $files,
        protected SettingStore $settingStore,
        protected PluginService $pluginService
    ) {
    }

    public function activate(string $theme): array
    {
        $validate = $this->validate($theme);

        if ($validate['error']) {
            return $validate;
        }

        if (setting('theme') && $theme == Theme::getThemeName()) {
            return [
                'error' => true,
                'message' => trans('packages/theme::theme.theme_activated_already', ['name' => $theme]),
            ];
        }

        $config = $this->getThemeConfig($theme);
        $inheritTheme = Arr::get($config, 'inherit');

        try {
            $content = BaseHelper::getFileData($this->getPath($theme, 'theme.json'));

            if ($inheritTheme && ! Theme::exists($inheritTheme)) {
                return [
                    'error' => true,
                    'message' => trans('packages/theme::theme.theme_inherit_not_found', ['name' => $inheritTheme]),
                ];
            }

            if (! empty($content)) {
                $requiredPlugins = Arr::get($content, 'required_plugins', []);
                if (! empty($requiredPlugins)) {
                    foreach ($requiredPlugins as $plugin) {
                        $this->pluginService->activate($plugin);
                    }
                }
            }
        } catch (Exception $exception) {
            return [
                'error' => true,
                'message' => $exception->getMessage(),
            ];
        }

        if (! empty($inheritTheme)) {
            $this->copyThemeOptions($theme);
            $this->copyThemeWidgets($theme);
        }

        Theme::setThemeName($theme);

        $published = $this->publishAssets($theme);

        if ($published['error']) {
            return $published;
        }

        $this->settingStore
            ->forceSet('theme', $theme)
            ->save();

        Helper::clearCache();

        return [
            'error' => false,
            'message' => trans('packages/theme::theme.active_success', ['name' => $theme]),
        ];
    }

    public function copyThemeOptions(string $theme): void
    {
        $fromTheme = setting('theme');

        if ($fromTheme === $theme) {
            return;
        }

        $themeOptions = ThemeOption::getOptions();

        $themeOptions = collect($themeOptions)
            ->filter(
                fn (mixed $value, string $key) => Str::startsWith($key, 'theme-' . $fromTheme . '-')
            )
            ->toArray();

        $copiedThemeOptions = [];

        $now = Carbon::now();

        foreach ($themeOptions as $key => $option) {
            $key = str_replace('theme-' . $fromTheme . '-', 'theme-' . $theme . '-', $key);

            $copiedThemeOptions[] = [
                'key' => $key,
                'value' => $option,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($copiedThemeOptions)) {
            Setting::query()
                ->insertOrIgnore($copiedThemeOptions);
        }
    }

    public function copyThemeWidgets(string $theme): void
    {
        $fromTheme = setting('theme');

        if ($fromTheme === $theme) {
            return;
        }

        $copiedWidgets = Widget::query()
            ->where('theme', $fromTheme)
            ->get()
            ->toArray();

        foreach ($copiedWidgets as $key => $widget) {
            $copiedWidgets[$key]['theme'] = $theme;
            $copiedWidgets[$key]['data'] = json_encode($widget['data']);
            unset($copiedWidgets[$key]['id']);
        }

        Widget::query()
            ->insertOrIgnore($copiedWidgets);
    }

    protected function validate(string $theme): array
    {
        $location = theme_path($theme);

        if (! $this->files->isDirectory($location)) {
            return [
                'error' => true,
                'message' => trans('packages/theme::theme.theme_is_not_existed'),
            ];
        }

        if (! $this->files->exists($location . '/theme.json')) {
            return [
                'error' => true,
                'message' => trans('packages/theme::theme.missing_json_file'),
            ];
        }

        return [
            'error' => false,
            'message' => trans('packages/theme::theme.theme_invalid'),
        ];
    }

    protected function getPath(string $theme, ?string $path = null): string
    {
        return rtrim(theme_path(), '/') . '/' . rtrim(ltrim(strtolower($theme), '/'), '/') . '/' . $path;
    }

    public function publishAssets(?string $theme = null): array
    {
        if ($theme) {
            $themes = [$theme];
        } else {
            $themes = BaseHelper::scanFolder(theme_path());
        }

        foreach ($themes as $key => $theme) {
            if (! $this->files->isDirectory(theme_path($theme))) {
                unset($themes[$key]);

                continue;
            }

            $resourcePath = $this->getPath($theme, 'public');

            $themePath = public_path('themes');
            if (! $this->files->isDirectory($themePath)) {
                $this->files->makeDirectory($themePath, 0755, true);
            } elseif (! $this->files->isWritable($themePath)) {
                return [
                    'error' => true,
                    'message' => trans('packages/theme::theme.folder_is_not_writeable', ['name' => $themePath]),
                ];
            }

            $publishPath = $themePath . '/' . ($theme == Theme::getThemeName() ? Theme::getPublicThemeName() : $theme);

            if (! $this->files->isDirectory($publishPath)) {
                $this->files->makeDirectory($publishPath, 0755, true);
            }

            $this->files->copyDirectory($resourcePath, $publishPath);

            $screenshot = $this->getPath($theme, 'screenshot.png');

            if ($this->files->exists($screenshot)) {
                $this->files->copy($screenshot, $publishPath . '/screenshot.png');
            }
        }

        if (! count($themes)) {
            return [
                'error' => true,
                'message' => 'No themes to publish assets.',
            ];
        }

        return [
            'error' => false,
            'message' => trans('packages/theme::theme.published_assets_success', ['themes' => implode(', ', $themes)]),
        ];
    }

    public function remove(string $theme): array
    {
        $validate = $this->validate($theme);

        if ($validate['error']) {
            return $validate;
        }

        if (Theme::getThemeName() === $theme) {
            return [
                'error' => true,
                'message' => trans('packages/theme::theme.cannot_remove_theme', ['name' => $theme]),
            ];
        }

        if (Theme::getInheritTheme() === $theme) {
            return [
                'error' => true,
                'message' => trans('packages/theme::theme.cannot_remove_inherit_theme', ['name' => $theme]),
            ];
        }

        $this->removeAssets($theme);

        $this->files->deleteDirectory($this->getPath($theme));
        Widget::query()
            ->where('theme', $theme)
            ->orWhere('theme', 'LIKE', $theme . '-%')
            ->delete();
        Setting::query()
            ->where('key', 'LIKE', ThemeOption::getOptionKey('%', theme: $theme))
            ->delete();

        event(new ThemeRemoveEvent($theme));

        return [
            'error' => false,
            'message' => trans('packages/theme::theme.theme_deleted', ['name' => $theme]),
        ];
    }

    public function removeAssets(string $theme): array
    {
        $validate = $this->validate($theme);

        if ($validate['error']) {
            return $validate;
        }

        $this->files->deleteDirectory(public_path('themes/' . $theme));

        return [
            'error' => false,
            'message' => trans('packages/theme::theme.removed_assets', ['name' => $theme]),
        ];
    }

    public function getThemeConfig(string $theme): array
    {
        $configFile = $this->getPath($theme, 'config.php');

        return $this->files->exists($configFile) ? $this->files->getRequire($configFile) : [];
    }
}
