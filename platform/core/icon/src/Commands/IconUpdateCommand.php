<?php

namespace Botble\Icon\Commands;

use Botble\Icon\Facades\Icon;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Utils as Psr7Utils;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Attribute\AsCommand;
use ZipArchive;

#[AsCommand(name: 'cms:icons:update', description: 'Update icons from Tabler Icons')]
class IconUpdateCommand extends Command
{
    public function handle(): int
    {
        $this->components->info('Fetching latest release of Tabler Icons...');

        $response = Http::withoutVerifying()
            ->timeout(300)
            ->get('https://api.github.com/repos/tabler/tabler-icons/releases/latest');

        if ($response->failed()) {
            $this->components->error($response->reason() ?: 'Failed to fetch latest release of Tabler Icons.');

            return self::FAILURE;
        }

        $response = $response->json();

        $tagName = str_replace('v', '', Arr::get($response, 'tag_name'));
        $downloadUrl = Arr::get($response, 'assets.0.browser_download_url');
        $folderName = "core-icons-$tagName";
        $destination = storage_path("app/$folderName");
        $zipDestination = "$destination.zip";

        if (
            ! File::exists($zipDestination)
            || Carbon::createFromTimestamp(filectime($zipDestination))->diffInHours() > 1
        ) {
            $this->components->info("Downloading v$tagName...");

            try {
                Http::withoutVerifying()
                    ->timeout(300)
                    ->sink(Psr7Utils::tryFopen($zipDestination, 'w+'))
                    ->get($downloadUrl);
            } catch (ConnectionException $exception) {
                File::delete($zipDestination);

                $this->components->error($exception->getMessage());

                return self::FAILURE;
            }
        }

        $this->components->info('Extracting files...');

        $zip = new ZipArchive();
        $zip->open($zipDestination);
        $zip->extractTo($destination);
        $zip->close();

        $this->components->info('Cleaning up...');

        $iconsDestination = Icon::iconPath();

        File::ensureDirectoryExists($iconsDestination);

        $currentIconsCount = count(File::allFiles($iconsDestination));

        foreach (File::allFiles("$destination/svg/outline") as $file) {
            $fileName = $file->getFilename();

            File::move($file->getPathname(), "$iconsDestination/$fileName");

            $content = File::get("$iconsDestination/$fileName");
            $content = preg_replace('/class="[^"]*"\s+/', '', $content);
            file_put_contents("$iconsDestination/$fileName", $content);
        }

        File::delete($zipDestination);
        File::deleteDirectory($destination);

        $newIconsCount = count(File::allFiles($iconsDestination));

        $this->components->info(
            sprintf(
                'Done! %s new icons added. Total %s icons.',
                number_format($newIconsCount - $currentIconsCount),
                number_format($newIconsCount)
            )
        );

        return self::SUCCESS;
    }
}
