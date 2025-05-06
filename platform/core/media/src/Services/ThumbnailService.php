<?php

namespace Botble\Media\Services;

use Botble\Base\Facades\BaseHelper;
use Botble\Media\Facades\RvMedia;
use Illuminate\Support\Facades\File;
use Intervention\Image\Encoders\AutoEncoder;
use Throwable;

class ThumbnailService
{
    protected string $imagePath;

    protected float $thumbRate;

    protected int|string|null $thumbWidth;

    protected int|string|null $thumbHeight;

    protected string $destinationPath;

    protected ?int $xCoordinate;

    protected ?int $yCoordinate;

    protected string $fitPosition;

    protected string $fileName;

    public function __construct(protected UploadsManager $uploadManager)
    {
        $this->thumbRate = 0.75;
        $this->xCoordinate = null;
        $this->yCoordinate = null;
        $this->fitPosition = setting('media_thumbnail_crop_position', 'center');
    }

    public function setImage(string $imagePath): self
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getImage(): string
    {
        return $this->imagePath;
    }

    public function setSize(int|string $width, int|string $height = 'auto'): self
    {
        $this->thumbWidth = $width;
        $this->thumbHeight = $height;

        if (! $height || $height == 'auto') {
            $this->thumbHeight = 0;
        } elseif ($height == 'rate') {
            $this->thumbHeight = (int) ($this->thumbWidth * $this->thumbRate);
        }

        if (! $width || $width == 'auto') {
            $this->thumbWidth = 0;
        } elseif ($width == 'rate') {
            $this->thumbWidth = (int) ($this->thumbHeight * $this->thumbRate);
        }

        return $this;
    }

    public function getSize(): array
    {
        return [$this->thumbWidth, $this->thumbHeight];
    }

    public function setDestinationPath(string $destinationPath): self
    {
        $this->destinationPath = $destinationPath;

        return $this;
    }

    public function setCoordinates(int $xCoordination, int $yCoordination): self
    {
        $this->xCoordinate = $xCoordination;
        $this->yCoordinate = $yCoordination;

        return $this;
    }

    public function getCoordinates(): array
    {
        return [$this->xCoordinate, $this->yCoordinate];
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function save(string $type = 'fit'): bool|string
    {
        $fileName = File::basename($this->imagePath);

        if ($this->fileName) {
            $fileName = $this->fileName;
        }

        $destinationPath = sprintf('%s/%s', trim($this->destinationPath, '/'), $fileName);

        $thumbImage = RvMedia::imageManager()->read($this->imagePath);

        if ($this->thumbWidth && ! $this->thumbHeight) {
            $type = 'width';
        } elseif ($this->thumbHeight && ! $this->thumbWidth) {
            $type = 'height';
        }

        switch ($type) {
            case 'width':
                if (! $this->thumbWidth) {
                    return $destinationPath;
                }

                $thumbImage->scale($this->thumbWidth);

                break;

            case 'height':
                if (! $this->thumbHeight) {
                    return $destinationPath;
                }

                $thumbImage->scale(null, $this->thumbHeight);

                break;

            case 'resize':
                if (! $this->thumbWidth || ! $this->thumbHeight) {
                    return $destinationPath;
                }

                $thumbImage->resize($this->thumbWidth, $this->thumbHeight);

                break;

            case 'crop':
                if (! $this->thumbWidth || ! $this->thumbHeight) {
                    return $destinationPath;
                }

                $thumbImage->crop($this->thumbWidth, $this->thumbHeight, $this->xCoordinate, $this->yCoordinate);

                break;

            case 'fit':
            default:
                if (! $this->thumbWidth || ! $this->thumbHeight) {
                    return $destinationPath;
                }

                $thumbImage->cover($this->thumbWidth, $this->thumbHeight, $this->fitPosition);

                break;
        }

        try {
            $this->uploadManager->saveFile($destinationPath, $thumbImage->encode(new AutoEncoder()));
        } catch (Throwable $exception) {
            BaseHelper::logError($exception);

            throw $exception;
        }

        return $destinationPath;
    }
}
