<?php

namespace Botble\Base\Commands;

use Botble\Base\Facades\BaseHelper;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

use function Laravel\Prompts\{info, progress};

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:cache:clear-expired', 'Remove all expired cache file/folder')]
class ClearExpiredCacheCommand extends Command
{
    protected int $expiredFileCount = 0;

    protected float $expiredFileSize = 0;

    protected int $activeFileCount = 0;

    protected float $activeFileSize = 0;

    protected FilesystemAdapter|Filesystem $disk;

    public function __construct()
    {
        parent::__construct();

        config(['filesystems.disks.fcache' => [
            'driver' => 'local',
            'root' => config('cache.stores.file.path'),
        ]]);

        $this->disk = Storage::disk('fcache');
    }

    public function handle(): int
    {
        $this->deleteExpiredFiles();
        $this->deleteEmptyFolders();
        $this->showResults();

        return self::SUCCESS;
    }

    protected function deleteExpiredFiles(): void
    {
        $files = $this->disk->allFiles();

        if (empty($files)) {
            return;
        }

        $progress = progress(
            label: 'Removing expired cache files',
            steps: count($files),
        );

        $progress->start();

        // Loop the files and get rid of any that have expired
        foreach ($files as $cacheFile) {
            // Ignore files that named with dot(.) at the beginning e.g: .gitignore
            if (str_starts_with($cacheFile, '.')) {
                continue;
            }

            // Get the full path of the file
            $fullPath = $this->disk->path($cacheFile);

            // Get the expiration time
            $handle = fopen($fullPath, 'r');
            $expire = fread($handle, 10);
            fclose($handle);

            // See if we have expired
            if (time() >= $expire) {
                // Delete the file
                $this->expiredFileSize += $this->disk->size($cacheFile);
                $this->disk->delete($cacheFile);
                $this->expiredFileCount++;
            } else {
                $this->activeFileCount++;
                $this->activeFileSize += $this->disk->size($cacheFile);
            }

            $progress->advance();
        }

        $progress->finish();
    }

    protected function deleteEmptyFolders(): void
    {
        $directories = $this->disk->allDirectories();
        $dirCount = count($directories);
        // looping backward to make sure subdirectories are deleted first
        while (--$dirCount >= 0) {
            if (! $this->disk->allFiles($directories[$dirCount])) {
                $this->disk->deleteDirectory($directories[$dirCount]);
            }
        }
    }

    public function showResults(): void
    {
        $expiredFileSize = BaseHelper::humanFilesize($this->expiredFileSize);
        $activeFileSize = BaseHelper::humanFilesize($this->activeFileSize);

        if ($this->expiredFileCount) {
            info("✔ $this->expiredFileCount expired cache files removed");
            info("✔ $expiredFileSize disk cleared");
        } else {
            info('✔ No expired cache file found!');
        }

        info("✔ $this->activeFileCount non-expired cache files remaining");
        info("✔ $activeFileSize disk space taken by non-expired cache files");
    }
}
