<?php

namespace Botble\Theme\Commands;

use Botble\Theme\Commands\Traits\ThemeTrait;
use Botble\Theme\Services\ThemeService;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:theme:remove', 'Remove an existing theme')]
class ThemeRemoveCommand extends Command implements PromptsForMissingInput
{
    use ThemeTrait;
    use ConfirmableTrait;

    public function handle(ThemeService $themeService): int
    {
        if (! $this->confirmToProceed('Are you sure you want to permanently delete?', true)) {
            return self::FAILURE;
        }

        $name = $this->getTheme();

        if (! preg_match('/^[a-z0-9\-]+$/i', $name)) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $result = $themeService->remove($name);

        if ($result['error']) {
            $this->components->error($result['message']);

            return self::FAILURE;
        }

        $this->components->info($result['message']);

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The theme name that you want to remove');
        $this->addOption('force', 'f', null, 'Force to remove theme without confirmation');
    }
}
