<?php

namespace Botble\Translation\Console;

use Botble\Translation\AutoTranslateManager;
use Botble\Translation\Manager;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:translation:auto-translate-theme', 'Auto translate theme from English to a new language')]
class AutoTranslateThemeCommand extends Command implements PromptsForMissingInput
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

        $translations = $manager->getThemeTranslations($locale);

        $this->components->info(sprintf('Translating %d words.', count($translations)));

        $count = 0;

        $autoTranslateManager = new AutoTranslateManager();

        foreach ($translations as $key => $translation) {
            if ($key !== $translation) {
                $this->components->info(sprintf('Translated already, skipped: <comment>%s</comment> => <info>%s</info>', $key, $translation));

                continue;
            }

            $translated = $autoTranslateManager->translate('en', $locale, $key);

            if ($translated != $key) {
                $this->components->info(sprintf('Translate: <comment>%s</comment> => <info>%s</info>', $key, $translated));

                $translations[$key] = $translated;

                $count++;
            }
        }

        $manager->saveThemeTranslations($locale, $translations);

        $this->components->info(sprintf('Done! %d has been translated.', $count));

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('locale', InputArgument::REQUIRED, 'The locale name that you want to translate');
        $this->addOption('override', 'o', null, 'Force translate theme again');
    }
}
