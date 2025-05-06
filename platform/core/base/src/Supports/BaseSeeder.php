<?php

namespace Botble\Base\Supports;

use Botble\Base\Events\FinishedSeederEvent;
use Botble\Base\Events\SeederPrepared;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\MetaBox as MetaBoxFacade;
use Botble\Base\Models\BaseModel;
use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Media\Facades\RvMedia;
use Botble\Media\Models\MediaFile;
use Botble\Media\Models\MediaFolder;
use Botble\Setting\Facades\Setting;
use Carbon\Carbon;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Traits\Conditionable;
use Symfony\Component\Process\Process;
use Throwable;

class BaseSeeder extends Seeder
{
    use Conditionable;

    protected Generator $faker;

    protected Carbon $now;

    protected string $basePath;

    public function uploadFiles(string $folder, ?string $basePath = null): array
    {
        $folderPath = $basePath ?: $this->getBasePath() . '/' . $folder;

        $folder = ltrim(str_replace(database_path('seeders/files'), '', $folderPath), '/');

        if (! File::isDirectory($folderPath)) {
            throw new FileNotFoundException('Folder not found: ' . $folderPath);
        }

        $storage = $this->getMediaStorage();

        if ($storage->exists($folder)) {
            $storage->deleteDirectory($folder);
        }

        MediaFile::query()->where('url', 'LIKE', $folder . '/%')->forceDelete();
        MediaFolder::query()->where('name', $folder)->forceDelete();

        $files = [];

        foreach (File::allFiles($folderPath) as $file) {
            try {
                $files[] = RvMedia::uploadFromPath($file, 0, $folder);
            } catch (Throwable $exception) {
                $this->command->warn('Error when uploading file: ' . $file->getRealPath());
                $this->command->warn($exception->getMessage());
            }
        }

        return $files;
    }

    protected function filePath(string $path, ?string $basePath = null): string
    {
        $filePath = ($basePath ? sprintf('%s/%s', $basePath, $path) : $this->getBasePath() . '/' . $path);
        $path = str_replace(database_path('seeders/files/'), '', $filePath);

        if ($this->getMediaStorage()->exists($path)) {
            return $path;
        }

        throw new FileNotFoundException('File not found: ' . $filePath);
    }

    protected function fileUrl(string $path, ?string $basePath = null, ?string $size = null)
    {
        $path = $this->filePath($path, $basePath);

        if ($size) {
            $path = RvMedia::getImageUrl($path, $size);

            $path = str_replace(url('/'), '', $path);
        }

        return $path;
    }

    public function prepareRun(): void
    {
        MediaFile::query()->truncate();
        MediaFolder::query()->truncate();

        $this->faker = $this->fake();

        Setting::newQuery()->truncate();

        Setting::forgetAll();

        Setting::forceSet('media_random_hash', md5((string) time()));

        Setting::set('api_enabled', 0);

        Setting::save();

        MetaBoxModel::query()->truncate();

        SeederPrepared::dispatch();
    }

    protected function random(int $from, int $to, array $exceptions = []): int
    {
        sort($exceptions); // lets us use break; in the foreach reliably
        $number = rand($from, $to - count($exceptions)); // or mt_rand()

        foreach ($exceptions as $exception) {
            if ($number >= $exception) {
                $number++; // make up for the gap
            } else { /*if ($number < $exception)*/
                break;
            }
        }

        return $number;
    }

    protected function finished(): void
    {
        FinishedSeederEvent::dispatch();
    }

    protected function fake(): Generator
    {
        if (isset($this->faker)) {
            return $this->faker;
        }

        if (! class_exists(Factory::class)) {
            $this->command->warn('It requires <info>fakerphp/faker</info> to run seeder. Need to run <info>composer install</info> to install it first.');

            try {
                $composer = new Composer($this->command->getLaravel()['files']);

                $process = new Process(array_merge($composer->findComposer(), ['install']));

                $process->start();

                $process->wait(function ($type, $buffer): void {
                    $this->command->line($buffer);
                });

                $this->command->warn('Please re-run <info>php artisan db:seed</info> command.');
            } catch (Throwable) {
            }

            exit(1);
        }

        $this->faker = fake();

        return $this->faker;
    }

    protected function now(): Carbon
    {
        if (isset($this->now)) {
            return $this->now;
        }

        $this->now = Carbon::now();

        return $this->now;
    }

    protected function removeBaseUrlFromString(string $value): ?string
    {
        return str_replace(url(''), '', $value);
    }

    protected function getFilesFromPath(string $path): Collection
    {
        $directoryPath = $this->getBasePath() . '/' . $path;

        $files = [];

        if (File::isDirectory($directoryPath)) {
            $files = array_map(fn ($item) => $path . '/' . $item, BaseHelper::scanFolder($directoryPath));
        }

        return collect($files);
    }

    protected function saveSettings(array $settings): void
    {
        Setting::delete(array_keys($settings));

        Setting::forceSet($settings)->save();
    }

    protected function getBasePath(): ?string
    {
        return $this->basePath ?? database_path('seeders/files');
    }

    protected function setBasePath(string $path): static
    {
        $this->basePath = $path;

        return $this;
    }

    protected function createMetadata(BaseModel $model, array $data): void
    {
        if (! isset($data['metadata']) || ! is_array($data['metadata'])) {
            return;
        }

        foreach ($data['metadata'] as $key => $value) {
            MetaBoxFacade::saveMetaBoxData($model, $key, $value);
        }
    }

    protected function getMediaStorage(): Filesystem
    {
        RvMedia::setUploadPathAndURLToPublic();

        return Storage::disk('public');
    }
}
