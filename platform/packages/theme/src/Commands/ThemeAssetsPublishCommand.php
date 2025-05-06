<?php

namespace Botble\Theme\Commands;

use Botble\Theme\Commands\Traits\ThemeTrait;
use Botble\Theme\Services\ThemeService;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:theme:assets:publish', 'Publish assets for a theme')]
class ThemeAssetsPublishCommand extends Command
{
    use ThemeTrait;

    public function handle(File $files, ThemeService $themeService): int
    {
        $name = $this->getTheme();

        if ($name && ! preg_match('/^[a-z0-9\-]+$/i', $name)) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        if ($name && ! $files->isDirectory($this->getPath())) {
            $this->components->error(sprintf('Theme "%s" is not exists.', $name));

            return self::FAILURE;
        }

        $result = $themeService->publishAssets($name);

        if ($result['error']) {
            $this->components->error($result['message']);

            return self::FAILURE;
        }

        $this->components->info($result['message']);

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'The theme name that you want to remove assets');
    }
}
