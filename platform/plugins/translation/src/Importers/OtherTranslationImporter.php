<?php

namespace Botble\Translation\Importers;

use Botble\Base\Supports\Language;
use Botble\DataSynchronize\Contracts\Importer\WithMapping;
use Botble\DataSynchronize\Importer\ImportColumn;
use Botble\DataSynchronize\Importer\Importer;
use Botble\Translation\Manager;
use Illuminate\Support\Facades\Auth;

class OtherTranslationImporter extends Importer implements WithMapping
{
    public function chunkSize(): int
    {
        return 1000;
    }

    public function getLabel(): string
    {
        return trans('plugins/translation::translation.panel.admin-translations.title');
    }

    public function columns(): array
    {
        $columns = [
            ImportColumn::make('key')
                ->rules(['required', 'string'], trans('plugins/translation::translation.import.rules.key'))
                ->heading('key'),
        ];

        foreach (Language::getAvailableLocales() as $locale) {
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
        return route('tools.data-synchronize.import.other-translations.validate');
    }

    public function getImportUrl(): string
    {
        return route('tools.data-synchronize.import.other-translations.store');
    }

    public function getExportUrl(): ?string
    {
        return Auth::user()->hasPermission('other-translations.export')
            ? route('tools.data-synchronize.export.other-translations.store')
            : null;
    }

    public function map(mixed $row): array
    {
        if (empty($row['key'])) {
            return [];
        }

        if (! str_contains($row['key'], '::')) {
            return $row;
        }

        [$group, $key] = explode('::', $row['key']);

        return [
            ...$row,
            'key' => $key,
            'group' => $group,
        ];
    }

    public function handle(array $data): int
    {
        $count = 0;

        $manager = app(Manager::class);

        $data = collect($data)->groupBy('group');

        foreach ($data as $group => $translations) {
            foreach (Language::getAvailableLocales() as $locale) {
                $localeTranslations = $translations->pluck($locale['locale'], 'key');

                $manager->updateTranslation(
                    $locale['locale'],
                    str_replace('/', DIRECTORY_SEPARATOR, $group),
                    $localeTranslations->all()
                );

                $count += count($localeTranslations);
            }
        }

        return $count;
    }

    public function headerToSnakeCase(): bool
    {
        return false;
    }
}
