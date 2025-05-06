<?php

namespace Botble\Base\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:log:clear', 'Clear log files')]
class ClearLogCommand extends Command
{
    public function handle(Filesystem $filesystem): int
    {
        $logPath = storage_path('logs');

        if (! $filesystem->isDirectory($logPath)) {
            return self::FAILURE;
        }

        $files = $filesystem->allFiles($logPath);

        if (empty($files)) {
            $this->components->info('No log files to clear!');

            return self::SUCCESS;
        }

        $this->newLine();

        $this->components->task('Clearing log files', function () use ($files, $filesystem): void {
            foreach ($files as $file) {
                $this->components->info(sprintf('Deleting [%s]', $file->getPathname()));
                $filesystem->delete($file->getPathname());
            }
        });

        $this->components->info('Clear log files successfully!');

        return self::SUCCESS;
    }
}
