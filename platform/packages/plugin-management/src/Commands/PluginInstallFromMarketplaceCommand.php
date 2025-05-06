<?php

namespace Botble\PluginManagement\Commands;

use Botble\PluginManagement\Commands\Concern\HasPluginNameValidation;
use Botble\PluginManagement\Services\MarketplaceService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Throwable;

#[AsCommand('cms:plugin:install-from-marketplace', 'Install a plugin from https://marketplace.botble.com')]
class PluginInstallFromMarketplaceCommand extends Command implements PromptsForMissingInput
{
    use HasPluginNameValidation;

    public function handle(MarketplaceService $marketplaceService): int
    {
        $name = $this->argument('name');

        $name = rtrim($name, '/');

        $this->validatePluginName($name);

        $plugin = strtolower($name);

        $response = $marketplaceService->callApi('post', '/products/check-update', [
            'products' => [$plugin => '0.0.0'],
        ]);

        if ($response->failed()) {
            $this->components->error($response->reason());

            return self::FAILURE;
        }

        $pluginId = $response->json('data.0.id');

        if (! $pluginId) {
            $this->components->error(sprintf('Plugin %s doesnt exists', $plugin));

            return self::FAILURE;
        }

        $response = $marketplaceService->callApi('get', '/products/' . $pluginId);

        if ($response instanceof JsonResponse) {
            $this->components->error($response->getData()->message);

            return self::FAILURE;
        }

        if ($response->failed()) {
            $this->components->error($response->reason());

            return self::FAILURE;
        }

        $detail = $response->json();

        $version = $detail['data']['minimum_core_version'];
        if (version_compare($version, get_core_version(), '>')) {
            $this->components->error(trans('packages/plugin-management::marketplace.minimum_core_version_error', compact('version')));

            return self::FAILURE;
        }

        $name = Str::afterLast($detail['data']['package_name'], '/');

        try {
            $response = $marketplaceService->beginInstall($pluginId, $name);

            if ($response instanceof JsonResponse) {
                $this->components->error($response->getData()->message);

                return self::FAILURE;
            }

            $this->components->info(sprintf('Installed plugin %s successfully', $plugin));

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The plugin that you want to install');
    }
}
