<?php

namespace Botble\Language\Listeners;

use Botble\Base\Facades\BaseHelper;
use Botble\Language\Facades\Language;
use Botble\Language\Models\LanguageMeta;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\Page\Models\Page;
use Botble\Slug\Models\Slug;
use Botble\Theme\Events\RenderingSingleEvent;
use Exception;

class AddHrefLangListener
{
    public function handle(RenderingSingleEvent $event): void
    {
        try {
            if (! defined('THEME_FRONT_HEADER')) {
                return;
            }

            add_filter(THEME_FRONT_HEADER, function ($header) use ($event) {
                $referenceType = $event->slug->reference_type;
                $referenceId = $event->slug->reference_id;

                if (! $referenceType && ! $referenceId) {
                    $referenceType = Page::class;
                }

                if (
                    ! in_array($referenceType, Language::supportedModels()) &&
                    (! is_plugin_active('language-advanced') || ! in_array($referenceType, LanguageAdvancedManager::supportedModels()))
                ) {
                    return $header;
                }

                $urls = [];

                $defaultLocale = Language::getDefaultLocale();

                $currentLocaleCode = Language::getCurrentLocaleCode();

                if ($referenceType == Page::class && ! $referenceId) {
                    foreach (Language::getSupportedLocales() as $localeCode => $properties) {
                        $urls[] = [
                            'url' => Language::getLocalizedURL($localeCode, url()->current(), [], false),
                            'lang_code' => $localeCode,
                        ];
                    }
                } else {
                    $languageMeta = LanguageMeta::query()
                        ->whereNot('language_meta.lang_meta_code', $currentLocaleCode)
                        ->join('language_meta as meta', 'meta.lang_meta_origin', 'language_meta.lang_meta_origin')
                        ->where([
                            'meta.reference_type' => $referenceType,
                            'meta.reference_id' => $referenceId,
                        ])
                        ->pluck('language_meta.lang_meta_code', 'language_meta.reference_id')
                        ->all();

                    $slug = Slug::query()
                        ->whereIn('reference_id', array_keys($languageMeta))
                        ->where('reference_type', $referenceType)
                        ->select(['key', 'prefix', 'reference_id'])
                        ->get();

                    foreach ($slug as $item) {
                        if (empty($languageMeta[$item->reference_id])) {
                            continue;
                        }

                        $locale = Language::getLocaleByLocaleCode($languageMeta[$item->reference_id]);

                        if ($locale == $defaultLocale && Language::hideDefaultLocaleInURL()) {
                            $locale = null;
                        }

                        $urls[] = [
                            'url' => url($locale . ($item->prefix ? '/' . $item->prefix : '') . '/' . $item->key),
                            'lang_code' => $languageMeta[$item->reference_id],
                        ];
                    }
                }

                Language::setSwitcherURLs($urls);

                return $header . view('plugins/language::partials.hreflang', compact('urls'))->render();
            }, 55);
        } catch (Exception $exception) {
            BaseHelper::logError($exception);
        }
    }
}
