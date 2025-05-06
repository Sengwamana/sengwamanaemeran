<?php

namespace Botble\Media\Commands;

use Botble\Media\Facades\RvMedia;
use Botble\Media\Models\MediaFile;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\{progress, table};

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:media:thumbnail:delete', 'Delete thumbnails for all images')]
class DeleteThumbnailCommand extends Command
{
    public function handle(): int
    {
        $this->components->info('Deleting thumbnails...');

        $files = MediaFile::query()->select(['url', 'mime_type', 'folder_id'])->get();

        if ($files->isEmpty()) {
            $this->components->info('No media files to generate thumbnails!');

            return self::SUCCESS;
        }

        $errors = [];

        $filesCount = $files->count();

        $progress = progress(
            label: sprintf('Processing %s %s...', number_format($filesCount), Str::plural('file', $filesCount)),
            steps: $filesCount,
        );

        foreach ($files as $file) {
            /**
             * @var MediaFile $file
             */
            $progress->label(sprintf('Processing %s...', $file->url));

            if (! $file->canGenerateThumbnails()) {
                continue;
            }

            try {
                RvMedia::deleteThumbnails($file);
            } catch (Exception $exception) {
                $errors[] = $file->url;
                $this->components->error($exception->getMessage());
            }

            $progress->advance();
        }

        $progress->finish();

        $this->components->info('Thumbnails deleted');

        $errors = array_unique($errors);

        $errors = array_map(fn ($item) => [$item], $errors);

        if ($errors) {
            $this->components->info('We are unable to regenerate thumbnail for these files:');

            table(['File directory'], $errors);

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
