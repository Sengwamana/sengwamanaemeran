<?php

namespace Botble\Translation\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Http\Controllers\SettingController;
use Botble\Theme\Facades\Theme;
use Botble\Translation\Http\Controllers\Concerns\HasMapTranslationsTable;
use Botble\Translation\Manager;
use Botble\Translation\Tables\ThemeTranslationTable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class ThemeTranslationController extends SettingController
{
    use HasMapTranslationsTable;

    public function index(Request $request, ThemeTranslationTable $translationTable)
    {
        $this->pageTitle(trans('plugins/translation::translation.theme-translations'));

        Assets::addStylesDirectly('vendor/core/plugins/translation/css/translation.css')
            ->addScriptsDirectly('vendor/core/plugins/translation/js/translation.js');

        [$groups, $group, $defaultLanguage, $translationTable]
            = $this->mapTranslationsTable($translationTable, $request);

        if ($request->expectsJson()) {
            return $translationTable->renderTable();
        }

        return view(
            'plugins/translation::theme-translations',
            compact('groups', 'group', 'defaultLanguage', 'translationTable')
        );
    }

    public function update(Request $request, Manager $manager)
    {
        if (! File::isDirectory(lang_path())) {
            File::makeDirectory(lang_path());
        }

        if (! File::isWritable(lang_path())) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/translation::translation.folder_is_not_writeable', ['lang_path' => lang_path()]));
        }

        $locale = $request->input('pk');

        if (! $locale) {
            return $this->updateResponse();
        }

        if (! $request->filled('name') || ! $request->filled('value')) {
            return $this->updateResponse();
        }

        $name = $request->input('name');
        $value = $request->input('value');

        $inheritTranslations = $manager->getInheritThemeTranslations($locale);
        $translations = $manager->getThemeTranslations($locale, false);
        $allTranslations =  $manager->getThemeTranslations($locale);

        if (! Arr::has($allTranslations, $request->input('name'))) {
            return $this->updateResponse();
        }

        if (Theme::hasInheritTheme()) {
            if (Arr::has($inheritTranslations, $name)) {
                $inheritTranslations[$name] = $value;
            }

            $manager->saveInheritThemeTranslation($locale, $inheritTranslations);
        }

        if (Arr::has($translations, $name)) {
            $translations[$name] = $value;
        }

        $manager->saveThemeTranslations($locale, $translations);

        return $this->updateResponse();
    }

    protected function updateResponse(): BaseHttpResponse
    {
        return $this
            ->httpResponse()
            ->setPreviousRoute('translations.theme-translations')
            ->withUpdatedSuccessMessage();
    }
}
