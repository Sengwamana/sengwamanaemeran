<?php

namespace Botble\Base\Rules;

use Botble\Media\Facades\RvMedia;
use Brick\Math\BigNumber;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class MediaImageRule implements ValidationRule
{
    public function __construct(
        protected array $mimeTypes = [],
        protected ?int $minSize = null,
        protected ?int $maxSize = null,
    ) {
        $this->mimeTypes = $mimeTypes ?: config('core.media.media.mime_types.image');
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (RvMedia::isUsingCloud() || (is_string($value) && Str::startsWith($value, ['http', 'https']))) {
            if (strlen($value) > 190) {
                $fail(trans('validation.max.string', ['max' => 255]));
            }

            return;
        }

        try {
            $file = $value instanceof UploadedFile ? $value : new UploadedFile(RvMedia::getRealPath($value), $value);
        } catch (FileNotFoundException) {
            $fail(trans('validation.exists'));

            return;
        }

        if (! in_array($file->getMimeType(), $this->mimeTypes)) {
            $fail(trans('validation.not_in'));
        }

        $this->validateFileSize($file->getSize(), $fail, $attribute);
    }

    protected function validateFileSize(int|false $size, Closure $fail, string $attribute): void
    {
        if ($size || ! ($this->minSize || $this->maxSize)) {
            return;
        }

        $bigNumber = BigNumber::of($size);

        if ($this->minSize && $bigNumber->isLessThan($this->minSize)) {
            $fail(trans('validation.min.file', ['attribute' => $attribute, 'min' => $this->minSize]));
        }

        if ($this->maxSize && $bigNumber->isGreaterThan($this->maxSize)) {
            $fail(trans('validation.max.file', ['attribute' => $attribute, 'max' => $this->maxSize]));
        }
    }
}
