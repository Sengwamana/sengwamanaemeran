<?php

namespace Botble\Translation\Exporters;

use Botble\Base\Supports\Language;
use Botble\DataSynchronize\Exporter\ExportColumn;
use Botble\DataSynchronize\Exporter\Exporter;
use Botble\Translation\Manager;
use Illuminate\Support\Collection;

class ThemeTranslationExporter extends Exporter
{
    public function getLabel(): string
    {
        return trans('plugins/translation::translation.panel.theme-translations.title');
    }

    public function columns(): array
    {
        $columns = [];

        foreach (Language::getAvailableLocales() as $locale) {
            $columns[] = ExportColumn::make($locale['locale'])->label($locale['locale'])->disabled();
        }

        return $columns;
    }

    public function collection(): Collection
    {
        $manager = app(Manager::class);

        $translations = collect($manager->getThemeTranslations('en'))
            ->transform(fn ($value, $key) => ['en' => $value])
            ->toArray();

        foreach (Language::getAvailableLocales() as $locale) {
            if ($locale['locale'] === 'en') {
                continue;
            }

            $currentTranslations = collect($manager->getThemeTranslations($locale['locale']))
                ->transform(fn ($value, $key) => [$locale['locale'] => $value])
                ->toArray();

            $translations = array_merge_recursive($translations, $currentTranslations);
        }

        return collect($translations);
    }
}
