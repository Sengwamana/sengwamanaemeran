<?php

namespace Botble\Language\Listeners;

use Botble\Base\Supports\Language as LanguageSupport;
use Botble\Language\Models\Language;

class CreateSelectedLanguageWhenInstallationFinished
{
    public function handle(): void
    {
        $siteLocale = app()->getLocale();

        if ($siteLocale === 'en') {
            return;
        }

        $language = collect(LanguageSupport::getListLanguages())
            ->firstWhere('0', $siteLocale);

        if (! $language || Language::query()->where('lang_locale', $language[0])->exists()) {
            return;
        }

        Language::query()->update(['lang_is_default' => false]);

        Language::query()->create([
            'lang_name' => $language[2],
            'lang_locale' => $language[0],
            'lang_is_default' => true,
            'lang_code' => $language[1],
            'lang_is_rtl' => $language[3] === 'rtl',
            'lang_flag' => $language[4],
        ]);
    }
}
