<?php

namespace Botble\Language\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Supports\Language;
use Botble\Language\Facades\Language as LanguageFacade;
use Botble\Language\Forms\Settings\LanguageSettingForm;
use Botble\Language\Http\Requests\LanguageRequest;
use Botble\Language\LanguageManager;
use Botble\Language\Models\Language as LanguageModel;
use Botble\Language\Models\LanguageMeta;
use Botble\Menu\Models\Menu;
use Botble\Menu\Models\MenuLocation;
use Botble\Menu\Models\MenuNode;
use Botble\Setting\Facades\Setting;
use Botble\Setting\Http\Controllers\SettingController;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Facades\ThemeOption;
use Botble\Translation\Manager;
use Botble\Widget\Models\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Throwable;

class LanguageController extends SettingController
{
    public function index()
    {
        $this->pageTitle(trans('plugins/language::language.name'));

        Assets::addScriptsDirectly(['vendor/core/plugins/language/js/language.js']);

        $languages = Language::getListLanguages();
        $flags = Language::getListLanguageFlags();
        $languageCodes = Language::getLanguageCodes();
        $localeKeys = Language::getLocaleKeys();
        $activeLanguages = LanguageModel::query()->orderBy('lang_order')->get();

        $languageSettingForm = LanguageSettingForm::create();

        return view(
            'plugins/language::index',
            compact('languages', 'flags', 'activeLanguages', 'languageSettingForm', 'languageCodes', 'localeKeys')
        );
    }

    public function store(LanguageRequest $request, LanguageManager $languageManager)
    {
        try {
            $language = LanguageModel::query()
                ->where('lang_code', $request->input('lang_code'))
                ->first();

            if ($language) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(trans('plugins/language::language.added_already'));
            }

            if (! LanguageModel::query()->exists()) {
                $request->merge(['lang_is_default' => 1]);
            }

            File::ensureDirectoryExists(lang_path('vendor'));

            if (! File::isWritable(lang_path()) || ! File::isWritable(lang_path('vendor'))) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(
                        trans('plugins/translation::translation.folder_is_not_writeable', ['lang_path' => lang_path()])
                    );
            }

            $locale = $request->input('lang_locale');

            $this->importLocaleIfMissing($locale);

            $language = LanguageModel::query()->create($request->except('lang_id'));

            $this->clearRoutesCache();

            event(new CreatedContentEvent(LANGUAGE_MODULE_SCREEN_NAME, $request, $language));

            $this->cloneMenusToLanguage($language);

            try {
                $models = $languageManager->supportedModels();

                if (LanguageModel::query()->count() == 1) {
                    foreach ($models as $model) {
                        if (! class_exists($model)) {
                            continue;
                        }

                        $ids = LanguageMeta::query()
                            ->where('reference_type', $model)
                            ->pluck('reference_id')
                            ->all();

                        $table = (new $model())->getTable();

                        $referenceIds = DB::table($table)
                            ->whereNotIn('id', $ids)
                            ->pluck('id')
                            ->all();

                        $data = [];
                        foreach ($referenceIds as $referenceId) {
                            $data[] = [
                                'reference_id' => $referenceId,
                                'reference_type' => $model,
                                'lang_meta_code' => $language->lang_code,
                                'lang_meta_origin' => md5($referenceId . $model . time()),
                            ];
                        }

                        LanguageMeta::query()->insert($data);
                    }
                }
            } catch (Throwable $exception) {
                return $this
                    ->httpResponse()
                    ->setData(view('plugins/language::partials.language-item', ['item' => $language])->render())
                    ->setMessage($exception->getMessage());
            }

            return $this
                ->httpResponse()
                ->setData(view('plugins/language::partials.language-item', ['item' => $language])->render())
                ->withCreatedSuccessMessage();
        } catch (Throwable $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    protected function importLocaleIfMissing(string $locale): bool
    {
        if (File::isDirectory(lang_path($locale))) {
            return false;
        }

        $importedLocale = false;

        if (is_plugin_active('translation')) {
            $result = app(Manager::class)->downloadRemoteLocale($locale);

            $importedLocale = ! $result['error'];
        }

        if (! $importedLocale) {
            $defaultLocale = lang_path('en');
            if (File::exists($defaultLocale)) {
                File::copyDirectory($defaultLocale, lang_path($locale));
            }

            $this->createLocaleInPath(lang_path('vendor/core'), $locale);
            $this->createLocaleInPath(lang_path('vendor/packages'), $locale);
            $this->createLocaleInPath(lang_path('vendor/plugins'), $locale);

            $this->copyThemeLangFiles($locale);
        }

        return $importedLocale;
    }

    public function update(Request $request)
    {
        try {
            $language = LanguageModel::query()->where('lang_id', $request->input('lang_id'))->first();
            abort_if(empty($language), 404);

            $language->fill($request->input());
            $language->save();

            $locale = $request->input('lang_locale');

            $this->importLocaleIfMissing($locale);

            $this->clearRoutesCache();

            event(new UpdatedContentEvent(LANGUAGE_MODULE_SCREEN_NAME, $request, $language));

            return $this
                ->httpResponse()
                ->setData(view('plugins/language::partials.language-item', ['item' => $language])->render())
                ->withUpdatedSuccessMessage();
        } catch (Throwable $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function postChangeItemLanguage(Request $request)
    {
        $referenceId = $request->input('reference_id') ?: $request->input('lang_meta_created_from');
        $currentLanguage = LanguageMeta::query()
            ->where([
                'reference_id' => $referenceId,
                'reference_type' => $request->input('reference_type'),
            ])
            ->first();

        $others = LanguageMeta::query();

        if ($currentLanguage) {
            $others = $others
                ->where('lang_meta_code', '!=', $request->input('lang_meta_current_language'))
                ->where('lang_meta_origin', $currentLanguage->origin);
        }

        $others = $others->select(['reference_id', 'lang_meta_code'])->get();

        $data = [];
        foreach ($others as $other) {
            $language = LanguageModel::query()
                ->where('lang_code', $other->lang_code)
                ->select([
                    'lang_flag',
                    'lang_name',
                    'lang_code',
                ])
                ->first();

            if (! empty($language) && ! empty($currentLanguage) && $language->lang_code != $currentLanguage->lang_meta_code) {
                $data[$language->lang_code]['lang_flag'] = $language->lang_flag;
                $data[$language->lang_code]['lang_name'] = $language->lang_name;
                $data[$language->lang_code]['reference_id'] = $other->reference_id;
            }
        }

        $languages = LanguageModel::query()->get();
        foreach ($languages as $language) {
            if (! array_key_exists(
                $language->lang_code,
                $data
            ) && $language->lang_code != $request->input('lang_meta_current_language')) {
                $data[$language->lang_code]['lang_flag'] = $language->lang_flag;
                $data[$language->lang_code]['lang_name'] = $language->lang_name;
                $data[$language->lang_code]['reference_id'] = null;
            }
        }

        return $this
            ->httpResponse()
            ->setData($data);
    }

    public function destroy(int|string $id)
    {
        $language = LanguageModel::query()->where('lang_id', $id)->first();

        return DeleteResourceAction::make($language)
            ->afterDeleting(function (DeleteResourceAction $action): void {
                $defaultLanguageId = false;

                if ($action->getModel()->lang_is_default) {
                    $defaultLanguage = LanguageModel::query()->where('lang_is_default', 1)->first();

                    if ($defaultLanguage) {
                        $defaultLanguageId = $defaultLanguage->lang_id;
                    }
                }

                $this->clearRoutesCache();

                $this->httpResponse()->setData($defaultLanguageId);
            });
    }

    public function getSetDefault(Request $request)
    {
        $newLanguageId = $request->input('lang_id');

        $newLanguage = LanguageModel::query()->where('lang_id', $newLanguageId)->firstOrFail();

        $newLanguageCode = $newLanguage->lang_code;

        $themeName = Theme::getThemeName();

        $defaultLanguage = LanguageFacade::getDefaultLanguage(['lang_id', 'lang_code']);

        if ($defaultLanguage) {
            $currentLanguageId = $defaultLanguage->lang_id;
            $currentLanguageCode = $defaultLanguage->lang_code;

            try {
                if ($currentLanguageId != $newLanguageId) {
                    if (! Widget::query()->where('theme', Widget::getThemeName($newLanguageCode))->exists()) {
                        $widgets = Widget::query()->where('theme', $themeName)->get();

                        foreach ($widgets as $widget) {
                            $replicated = $widget->replicate();

                            $widget->theme = Widget::getThemeName($currentLanguageCode);
                            $widget->save();

                            $replicated->save();
                        }
                    } else {
                        $currentWidgets = Widget::query()->where('theme', Widget::getThemeName($newLanguageCode))->get();

                        Widget::query()->where('theme', Widget::getThemeName($newLanguageCode))->delete();

                        $widgets = Widget::query()->where('theme', $themeName)->get();

                        foreach ($widgets as $widget) {
                            $widget->theme = Widget::getThemeName($currentLanguageCode);
                            $widget->save();
                        }

                        foreach ($currentWidgets as $widget) {
                            $widget = $widget->replicate();
                            $widget->theme = $themeName;
                            $widget->save();
                        }
                    }

                    $themeOptionKey = ThemeOption::getOptionKey('', $currentLanguageCode);

                    if (! Setting::newQuery()->where(
                        'key',
                        'LIKE',
                        ThemeOption::getOptionKey('%', $newLanguageCode)
                    )->exists()) {
                        $themeOptions = Setting::newQuery()->where('key', 'LIKE', $themeOptionKey . '%')->get();

                        foreach ($themeOptions as $themeOption) {
                            $replicated = $themeOption->replicate();

                            $themeOption->key = str_replace(
                                ThemeOption::getOption($themeOption->key, $defaultLanguage->lang_code),
                                ThemeOption::getOption($themeOption->key),
                                $themeOption->key
                            );
                            $themeOption->save();

                            $replicated->save();
                        }
                    } else {
                        $currentThemeOptions = Setting::newQuery()->where(
                            'key',
                            'LIKE',
                            ThemeOption::getOptionKey('%', $newLanguageCode)
                        )->get();

                        Setting::newQuery()
                            ->where('key', 'LIKE', ThemeOption::getOptionKey('%', $newLanguageCode))
                            ->delete();

                        $themeOptions = Setting::newQuery()->where('key', 'LIKE', $themeOptionKey . '%')->get();

                        foreach ($themeOptions as $themeOption) {
                            $themeOption->key = str_replace(
                                $themeOptionKey,
                                ThemeOption::getOptionKey('', $currentLanguageCode),
                                $themeOption->key
                            );

                            $themeOption->save();
                        }

                        foreach ($currentThemeOptions as $themeOption) {
                            $themeOption = $themeOption->replicate();

                            $themeOption->key = str_replace(
                                ThemeOption::getOptionKey('', $newLanguageCode),
                                $themeOptionKey,
                                $themeOption->key
                            );

                            $themeOption->save();
                        }
                    }
                }
            } catch (Throwable $exception) {
                BaseHelper::logError($exception);
            }
        }

        LanguageModel::query()->where('lang_is_default', 1)->update(['lang_is_default' => 0]);

        $newLanguage->lang_is_default = 1;
        $newLanguage->save();

        $this->clearRoutesCache();

        event(new UpdatedContentEvent(LANGUAGE_MODULE_SCREEN_NAME, $request, $newLanguage));

        return $this
            ->httpResponse()
            ->withUpdatedSuccessMessage();
    }

    public function getLanguage(Request $request)
    {
        $language = LanguageModel::query()->where('lang_id', $request->input('lang_id'))->first();

        return $this
            ->httpResponse()
            ->setData($language);
    }

    public function getChangeDataLanguage($code, LanguageManager $language)
    {
        $previousUrl = strtok(URL::previous(), '?');

        $queryString = null;
        if ($code !== $language->getDefaultLocaleCode()) {
            $queryString = '?' . http_build_query([LanguageFacade::refLangKey() => $code]);
        }

        return redirect()->to($previousUrl . $queryString);
    }

    protected function createLocaleInPath(string $path, string $locale): int
    {
        $folders = File::directories($path);

        foreach ($folders as $module) {
            foreach (File::directories($module) as $item) {
                if (File::name($item) == 'en') {
                    File::copyDirectory($item, $module . '/' . $locale);
                }
            }
        }

        return count($folders);
    }

    public function clearRoutesCache(): bool
    {
        $path = app()->getCachedRoutesPath();

        foreach (LanguageFacade::getSupportedLanguagesKeys() as $locale) {
            if (! $locale) {
                $locale = LanguageFacade::getDefaultLocale();
            }

            $path = substr($path, 0, -4) . '_' . $locale . '.php';

            if (File::exists($path)) {
                File::delete($path);
            }
        }

        return true;
    }

    protected function copyThemeLangFiles(mixed $locale)
    {
        if (Theme::hasInheritTheme()) {
            $this->copyThemeLangFilesFromTheme(Theme::getInheritTheme(), $locale);
        }

        $this->copyThemeLangFilesFromTheme(Theme::getThemeName(), $locale);
    }

    protected function copyThemeLangFilesFromTheme(string $theme, string $locale): void
    {
        $themeLangPath = theme_path($theme . '/lang');

        if (! File::isDirectory($themeLangPath)) {
            return;
        }
        $themeLocale = Arr::first(BaseHelper::scanFolder($themeLangPath));
        $themeLocalePath = $themeLangPath . '/' . $themeLocale;

        if (! $themeLocale) {
            return;
        }

        File::copy($themeLocalePath, lang_path($locale . '.json'));
    }

    protected function cloneMenusToLanguage(LanguageModel $language): void
    {
        $menus = Menu::query()
            ->with(['menuNodes', 'locations'])
            ->join('language_meta', 'language_meta.reference_id', '=', 'menus.id')
            ->where('language_meta.reference_type', Menu::class)
            ->where('language_meta.lang_meta_code', LanguageFacade::getDefaultLocaleCode())
            ->select('menus.*')
            ->get();

        foreach ($menus as $menu) {
            /**
             * @var Menu $menuItem
             */
            $menuItem = $menu->replicate();
            $menuItem->slug = $menu->slug . '-' . $language->lang_code;
            $menuItem->save();

            $originValue = LanguageMeta::query()
                ->where('reference_id', $menu->id)
                ->where('reference_type', Menu::class)
                ->value('lang_meta_origin');

            LanguageMeta::saveMetaData($menuItem, $language->lang_code, $originValue);

            foreach ($menu->locations as $location) {
                $menuLocationItem = $location->replicate();
                $menuLocationItem->menu_id = $menuItem->getKey();
                $menuLocationItem->save();

                $originValue = LanguageMeta::query()
                    ->where('reference_id', $location->id)
                    ->where('reference_type', MenuLocation::class)
                    ->value('lang_meta_origin');

                LanguageMeta::saveMetaData($menuLocationItem, $language->lang_code, $originValue);
            }

            foreach ($menu->menuNodes as $menuNode) {
                $menuNodeItem = $menuNode->replicate();
                $menuNodeItem->menu_id = $menuItem->getKey();
                $menuNodeItem->save();

                $originValue = LanguageMeta::query()
                    ->where('reference_id', $menuNode->id)
                    ->where('reference_type', MenuNode::class)
                    ->value('lang_meta_origin');

                LanguageMeta::saveMetaData($menuNodeItem, $language->lang_code, $originValue);
            }
        }
    }
}
