<?php

namespace Botble\Translation\Http\Controllers\Concerns;

use Botble\Base\Supports\Language;
use Botble\Translation\Tables\ThemeTranslationTable;
use Botble\Translation\Tables\TranslationTable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

trait HasMapTranslationsTable
{
    protected function mapTranslationsTable(ThemeTranslationTable|TranslationTable $table, Request $request): array
    {
        $locales = Language::getAvailableLocales();
        $defaultLanguage = Language::getDefaultLanguage();

        if (! count($locales)) {
            $locales = [
                'en' => $defaultLanguage,
            ];
        }

        $currentLocale = $request->has('ref_lang') ? $request->input('ref_lang') : app()->getLocale();

        $group = Arr::first($locales, fn ($item) => $item['locale'] == $currentLocale);

        if (! $group) {
            $group = $defaultLanguage;
        }

        $table->setLocale($group['locale']);

        return [
            $locales,
            $group,
            $defaultLanguage,
            $table,
        ];
    }
}
