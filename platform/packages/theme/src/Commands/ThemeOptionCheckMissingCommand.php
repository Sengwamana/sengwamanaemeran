<?php

namespace Botble\Theme\Commands;

use Botble\Language\Facades\Language;
use Botble\Setting\Facades\Setting;
use Botble\Theme\Events\RenderingThemeOptionSettings;
use Botble\Theme\Facades\ThemeOption;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use function Laravel\Prompts\table;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:theme:options:check', 'Check difference theme options between database and option definitions')]
class ThemeOptionCheckMissingCommand extends Command
{
    public function handle(): int
    {
        $isReverse = $this->option('reverse');

        RenderingThemeOptionSettings::dispatch();

        $fields = array_map(
            fn (string $name) => ThemeOption::getOptionKey($name),
            array_keys(Arr::get(ThemeOption::getFields(), 'theme', []))
        );

        $existsOptionQuery = Setting::newQuery();
        $existsOptionQuery->where('key', 'LIKE', ThemeOption::getOptionKey('%'));

        if (is_plugin_active('language')) {
            foreach (Language::getSupportedLanguagesKeys() as $language) {
                $existsOptionQuery->where('key', 'NOT LIKE', ThemeOption::getOptionKey('%', $language));
            }
        }

        $existsOptions = $existsOptionQuery->pluck('key')->all();
        $missingKeys = $isReverse
            ? $this->missingKeys($existsOptions, $fields)
            : $this->missingKeys($fields, $existsOptions);

        if ($missingKeys->isEmpty()) {
            $this->components->info('No missing option found!');

            return self::SUCCESS;
        }

        $missingKeysCount = $missingKeys->count();
        $pluralKeyWord = Str::plural('key', $missingKeysCount);

        $this->components->info(
            $isReverse
                ? sprintf('We found <info>%s</info> %s are not exists in settings table (database).', $missingKeysCount, $pluralKeyWord)
                : sprintf('We found <info>%s</info> %s are not defined in theme options.', $missingKeysCount, $pluralKeyWord)
        );

        table(['#', 'Key'], $missingKeys->toArray());

        return self::SUCCESS;
    }

    protected function missingKeys(array $items, array $origin): Collection
    {
        return collect($items)
            ->filter(fn ($item) => ! in_array($item, $origin))
            ->values()
            ->map(fn ($item, $key) => [$key, $item]);
    }

    protected function configure(): void
    {
        $this->addOption('reverse', 'R', null, 'Reverse results');
    }
}
