<?php

namespace Botble\Translation\Console;

use Botble\Translation\Manager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:translations:update-theme-translations', 'Update theme translations')]
class UpdateThemeTranslationCommand extends Command
{
    public function handle(Manager $manager): int
    {
        $count = $manager->updateThemeTranslations();

        $this->components->info(sprintf('Found %s keys.', number_format($count)));

        return self::SUCCESS;
    }
}
