<?php

namespace Botble\Media\Commands;

use Botble\Base\Facades\BaseHelper;
use Botble\Media\Facades\RvMedia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand('cms:media:image:crop', 'Crop image(s).')]
class CropImageCommand extends Command
{
    public function handle(): int
    {
        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        $path = $this->argument('image');
        $orderedNumber = (bool) $this->option('ordered-number');

        if (! Str::startsWith($path, '/')) {
            $path = base_path($path);
        }

        if (File::isDirectory($path)) {
            $directoryPath = rtrim($path, DIRECTORY_SEPARATOR);
        } else {
            $directoryPath = File::dirname($path);
        }

        $saveTo = $this->option('directory');
        $saveTo = $saveTo ?: BaseHelper::joinPaths([$directoryPath, 'cropped']);

        if (! Str::startsWith($saveTo, '/')) {
            $saveTo = base_path($saveTo);
        }

        if ($this->option('empty') && File::isDirectory($saveTo)) {
            $this->components->info('Emptying the directory: ' . $saveTo);
            File::deleteDirectory($saveTo);
        }

        File::ensureDirectoryExists($saveTo);

        $dimension = $this->option('dimension');

        [$width, $height] = $this->extractDimension($dimension);

        if (! $width) {
            $width = (int) $this->option('width');
        }

        if (! $height) {
            $height = (int) $this->option('height');
        }

        if (! $width || ! $height) {
            $this->components->error('The dimension is required.');

            return static::FAILURE;
        }

        if ($width <= 0 || $height <= 0) {
            $this->components->error('The width and height must be greater than 0.');

            return static::FAILURE;
        }

        $this->components->info('Resizing image(s) to ' . $width . 'x' . $height . 'px.');

        if (File::isFile($path)) {
            $this->cropImage($path, $width, $height, $saveTo, $orderedNumber ? '1' : null);

            $this->renderMessage(1, $saveTo);

            return static::SUCCESS;
        }

        foreach ($files = File::files($path) as $index => $file) {
            $number = $index + 1;
            $this->cropImage($file->getRealPath(), $width, $height, $saveTo, $orderedNumber ? (string) $number : null);
        }

        $this->renderMessage(count($files), $saveTo);

        return static::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('image', InputArgument::REQUIRED, 'The image to crop.')
            ->addOption('dimension', null, InputOption::VALUE_REQUIRED, 'The dimension of the crop (e.g 1920x1080).')
            ->addOption('width', null, InputOption::VALUE_REQUIRED, 'The width of the crop in px (e.g 1920).')
            ->addOption('height', null, InputOption::VALUE_REQUIRED, 'The height of the crop in px (e.g 1080).')
            ->addOption('directory', null, InputOption::VALUE_REQUIRED, 'The directory to save the cropped image.')
            ->addOption('empty', null, InputOption::VALUE_NONE, 'Empty the directory before saving the cropped image.')
            ->addOption('ordered-number', null, InputOption::VALUE_NONE, 'Auto rename to ordered number.')
        ;
    }

    protected function cropImage(string $path, int $width, int $height, ?string $saveTo, ?string $newName = null): void
    {
        $this->components->task(
            sprintf('Cropping %s', $path),
            function () use ($newName, $saveTo, $height, $width, $path): void {
                $thumbImage = RvMedia::imageManager()->read($path);
                $thumbImage->cover($width, $height);
                $extension = File::extension($path);

                if ($newName) {
                    $filename = Str::endsWith($newName, '.' . $extension) ? $newName : $newName . '.' . $extension;
                } else {
                    $filename = File::name($path) . "_{$width}x{$height}." . $extension;
                }

                $thumbImage->save(
                    BaseHelper::joinPaths([$saveTo, $filename])
                );
            }
        );
    }

    protected function extractDimension(?string $dimension): array
    {
        if (! $dimension) {
            return [null, null];
        }

        return explode('x', $dimension);
    }

    public function renderMessage(int $numberOfFiles, string $saveTo): void
    {
        $this->components->info(sprintf('Cropped %d %s successfully!', $numberOfFiles, Str::plural('file', $numberOfFiles)));

        $this->components->info('Saved to: ' . $saveTo);
    }
}
