<?php

namespace Botble\PluginManagement\Commands;

use Botble\Base\Facades\BaseHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:plugin:update-version-info', 'Updater version number in plugin.json file.')]
class PluginUpdateVersionInfoCommand extends Command
{
    public function handle(): int
    {
        $filePath = $this->argument('path');

        if ($filePath) {
            if (! File::exists($filePath)) {
                $this->components->error('The JSON file does not exist.');

                return self::FAILURE;
            }

            $version = $this->updateVersionNumber($filePath);

            $this->components->info(sprintf('Updated in %s to %s successfully!', $filePath, $version));

            return self::SUCCESS;
        }

        $plugins = BaseHelper::scanFolder(plugin_path());
        if (! empty($plugins)) {
            foreach ($plugins as $plugin) {
                $configFile = plugin_path("$plugin/plugin.json");
                if (! File::exists($configFile)) {
                    continue;
                }

                $this->updateVersionNumber($configFile);
            }
        }

        $this->components->info(sprintf('Updated version for %d plugins successfully!', count($plugins)));

        return self::SUCCESS;
    }

    protected function updateVersionNumber(string $configFile): string
    {
        $version = '';

        $content = BaseHelper::getFileData($configFile);

        if (! empty($content)) {
            $version = Arr::get($content, 'version');

            for ($newVersion = explode('.', $version), $i = count($newVersion) - 1; $i > -1; --$i) {
                if (++$newVersion[$i] < 10 || ! $i) {
                    break;
                }

                $newVersion[$i] = 0;
            }

            $version = implode('.', $newVersion);

            Arr::set($content, 'version', $version);

            BaseHelper::saveFileData($configFile, $content);
        }

        return $version;
    }

    protected function configure(): void
    {
        $this->addArgument('path', InputArgument::OPTIONAL, 'The path to JSON file');
    }
}
