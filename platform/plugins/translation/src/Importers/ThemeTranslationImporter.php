<?php

namespace Botble\Translation\Importers;

use Botble\Base\Supports\Language;
use Botble\DataSynchronize\Contracts\Importer\WithMapping;
use Botble\DataSynchronize\Importer\ImportColumn;
use Botble\DataSynchronize\Importer\Importer;
use Botble\Translation\Manager;
use Illuminate\Support\Facades\Auth;

class ThemeTranslationImporter extends Importer implements WithMapping
{
    public function chunkSize(): int
    {
        return 100;
    }

    public function getLabel(): string
    {
        return trans('plugins/translation::translation.panel.theme-translations.title');
    }

    public function columns(): array
    {
        $columns = [
            ImportColumn::make('en')
                ->label('en')
                ->rules(
                    ['nullable', 'string', 'max:10000'],
                    trans(
                        'plugins/translation::translation.import.rules.trans',
                        ['max' => 10000]
                    )
                ),
        ];

        foreach (Language::getAvailableLocales() as $locale) {
            if ($locale['locale'] === 'en') {
                continue;
            }

            $columns[] = ImportColumn::make($locale['locale'])
                ->label($locale['locale'])
                ->rules(
                    ['nullable', 'string', 'max:10000'],
                    trans(
                        'plugins/translation::translation.import.rules.trans',
                        ['max' => 10000]
                    )
                );
        }

        return $columns;
    }

    public function getValidateUrl(): string
    {
        return route('tools.data-synchronize.import.theme-translations.validate');
    }

    public function getImportUrl(): string
    {
        return route('tools.data-synchronize.import.theme-translations.store');
    }

    public function getExportUrl(): ?string
    {
        return Auth::user()->hasPermission('theme-translations.export')
            ? route('tools.data-synchronize.export.theme-translations.store')
            : null;
    }

    public function map(mixed $row): array
    {
        return $row;
    }

    public function handle(array $data): int
    {
        $count = 0;

        $manager = app(Manager::class);

        foreach (Language::getAvailableLocales() as $locale) {
            $locale = $locale['locale'];

            if ($locale === 'en') {
                continue;
            }

            $translations = $manager->getThemeTranslations($locale);

            foreach ($data as $row) {
                if (! $locale || ! isset($row[$locale])) {
                    continue;
                }

                if (isset($translations[$row['en']])) {
                    $translations[$row['en']] = $row[$locale];
                } else {
                    $translations[] = [$row['en'] => $row[$locale]];
                }
            }

            if ($translations) {
                $manager->saveThemeTranslations($locale, $translations);
                $count += count($translations);
            }
        }

        return $count;
    }

    public function headerToSnakeCase(): bool
    {
        return false;
    }
}
