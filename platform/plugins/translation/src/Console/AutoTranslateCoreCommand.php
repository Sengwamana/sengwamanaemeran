<?php

namespace Botble\Translation\Console;

use Botble\Translation\AutoTranslateManager;
use Botble\Translation\Manager;
use Botble\Translation\Services\GetGroupedTranslationsService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:translation:auto-translate-core', 'Auto translate core from English to a new language')]
class AutoTranslateCoreCommand extends Command implements PromptsForMissingInput
{
    public function handle(Manager $manager): int
    {
        $locale = $this->argument('locale');

        if (! preg_match('/^[a-z0-9\-_]+$/i', $locale)) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        if ($this->option('override')) {
            $manager->deleteLocale($locale);
        }

        $manager->downloadLocaleIfMissing($locale);

        $this->components->info(sprintf('Translating %s...', $locale));

        $translations = $this->getTranslations($locale);

        $this->components->info(sprintf('Translating %d words.', count($translations)));

        $count = 0;

        $autoTranslateManager = new AutoTranslateManager();

        foreach ($translations->groupBy('group')->toArray() as $group => $translationGroup) {
            $autoTranslations = [];

            foreach ($translationGroup as $translation) {

                if (is_array($translation[$locale])) {
                    continue;
                }

                if ($translation['en'] !== $translation[$locale]) {
                    $this->components->info(sprintf('Translated already, skipped: <comment>%s</comment> => <info>%s</info>', $translation['en'], $translation[$locale]));

                    continue;
                }

                [$group, $key] = explode('::', $translation['key']);

                $translated = $autoTranslateManager->translate('en', $locale, $translation[$locale]);

                $autoTranslations[$key] = $translated;

                $this->components->info(sprintf('Translate: <comment>%s</comment> => <info>%s</info>', $translation[$locale], $translated));

                $count++;
            }

            $manager->updateTranslation(
                $locale,
                str_replace('/', DIRECTORY_SEPARATOR, $group),
                $autoTranslations
            );

            $count += count($translationGroup);
        }

        $this->components->info(sprintf('Done! %d has been translated.', $count));

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('locale', InputArgument::REQUIRED, 'The locale name that you want to translate');
        $this->addOption('override', 'o', null, 'Force translate core again');
    }

    protected function getTranslations(string $locale): Collection
    {
        return (new GetGroupedTranslationsService())
            ->handle()
            ->transform(fn ($translation) => [
                'key' => sprintf('%s::%s', $translation['group'], $translation['key']),
                'en' => $translation['value'],
            ])
            ->transform(function ($translation) use ($locale) {
                [$group, $key] = explode('::', $translation['key']);

                return [
                    ...$translation,
                    'group' => $group,
                    $locale => trans(
                        Str::of($group)
                            ->replaceLast(DIRECTORY_SEPARATOR, '::')
                            ->append(".$key")
                            ->toString(),
                        [],
                        $locale
                    ),
                ];
            });
    }
}
