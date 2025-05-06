<?php

namespace Botble\Media\Commands;

use Botble\Media\Chunks\Storage\ChunkStorage;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:media:chunks:clear', 'Clears the chunks upload directory. Deletes only .part objects.')]
class ClearChunksCommand extends Command
{
    public function handle(ChunkStorage $storage): int
    {
        $oldFiles = $storage->oldChunkFiles();

        if ($oldFiles->isEmpty()) {
            $this->components->warn('No chunk files found');

            return self::SUCCESS;
        }

        $filesCount = $oldFiles->count();

        $this->components->info(sprintf('Found %d %s', $filesCount, Str::plural('file', $filesCount)));
        $deleted = 0;

        foreach ($oldFiles as $file) {
            $this->components->info(sprintf('Deleting %s', $file));

            if ($file->delete()) {
                ++$deleted;
            } else {
                $this->components->error(sprintf('Failed to delete %s', $file));
            }
        }

        $this->components->info(sprintf('Deleted %d %s', $deleted, Str::plural('file', $deleted)));

        return self::SUCCESS;
    }
}
