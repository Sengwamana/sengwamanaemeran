<?php

namespace Botble\Media\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Models\BaseModel;
use Botble\Media\Facades\RvMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class MediaFile extends BaseModel
{
    use SoftDeletes;

    protected $table = 'media_files';

    protected $fillable = [
        'name',
        'mime_type',
        'type',
        'size',
        'url',
        'options',
        'folder_id',
        'user_id',
        'alt',
        'visibility',
    ];

    protected $casts = [
        'options' => 'json',
        'name' => SafeContent::class,
    ];

    protected $appends = [
        'indirect_url',
    ];

    protected static function booted(): void
    {
        static::forceDeleted(fn (MediaFile $file) => RvMedia::deleteFile($file));

        static::addGlobalScope('ownMedia', function (Builder $query): void {
            if (RvMedia::canOnlyViewOwnMedia()) {
                $query->where('media_files.user_id', auth()->id());
            }
        });
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id')->withDefault();
    }

    protected function type(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $type = 'document';

                foreach (RvMedia::getConfig('mime_types', []) as $key => $value) {
                    if (in_array($attributes['mime_type'], $value)) {
                        $type = $key;

                        break;
                    }
                }

                return $type;
            }
        );
    }

    protected function humanSize(): Attribute
    {
        return Attribute::get(fn () => BaseHelper::humanFilesize($this->size));
    }

    protected function icon(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $types = [
                    'jpeg' => [
                        'image/jpeg',
                        'image/jpg',
                    ],
                    'png' => [
                        'image/png',
                    ],
                    'gif' => [
                        'image/gif',
                    ],
                    'video' => [
                        'video/mp4',
                        'video/m4v',
                        'video/mov',
                        'video/quicktime',
                    ],
                    'document' => [
                        'text/plain',
                        'text/csv',
                    ],
                    'zip' => [
                        'application/zip',
                        'application/x-zip-compressed',
                        'application/x-compressed',
                        'multipart/x-zip',
                    ],
                    'audio' => [
                        'audio/mpeg',
                        'audio/mp3',
                        'audio/wav',
                    ],
                    'docx' => [
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ],
                    'doc' => [
                        'application/msword',
                    ],
                    'excel' => [
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                        'application/excel',
                        'application/x-excel',
                        'application/x-msexcel',
                    ],
                    'pdf' => [
                        'application/pdf',
                    ],
                    'powerpoint' => [
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    ],
                ];

                $type = $this->type;

                foreach ($types as $key => $value) {
                    if (in_array($attributes['mime_type'], $value)) {
                        $type = $key;

                        break;
                    }
                }

                $icon = match ($type) {
                    'image' => 'ti ti-photo',
                    'video' => 'ti ti-video',
                    'pdf' => 'ti ti-file-type-pdf',
                    'excel' => 'ti ti-file-spreadsheet',
                    'zip' => 'ti ti-file-zip',
                    'docx' => 'ti ti-file-type-docx',
                    'doc' => 'ti ti-file-type-doc',
                    'powerpoint' => 'ti ti-presentation',
                    'jpeg' => 'ti ti-jpg',
                    'png' => 'ti ti-png',
                    'gif' => 'ti ti-gif',
                    default => 'ti ti-file',
                };

                return apply_filters('cms_media_file_icon', BaseHelper::renderIcon($icon), $this);
            }
        );
    }

    protected function previewUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            $preview = null;

            switch ($this->type) {
                case 'image':
                case 'jpeg':
                case 'png':
                case 'gif':
                    if ($this->visibility === 'public') {
                        $preview = RvMedia::url($this->url);
                    }

                    break;
                case 'text':
                case 'video':
                    $preview = RvMedia::url($this->url);

                    break;
                case 'document':
                case 'pdf':
                case 'doc':
                case 'docx':
                case 'excel':
                case 'powerpoint':
                    if ($this->mime_type === 'application/pdf' && $this->visibility === 'public') {
                        $preview = RvMedia::url($this->url);

                        break;
                    }

                    $config = config('core.media.media.preview.document', []);
                    if (
                        $this->visibility === 'public' &&
                        Arr::get($config, 'enabled') &&
                        Request::ip() !== '127.0.0.1' &&
                        in_array($this->mime_type, Arr::get($config, 'mime_types', [])) &&
                        $url = Arr::get($config, 'providers.' . Arr::get($config, 'default'))
                    ) {
                        $preview = Str::replace('{url}', urlencode(RvMedia::url($this->url)), $url);
                    }

                    break;
            }

            return $preview;
        });
    }

    protected function previewType(): Attribute
    {
        return Attribute::get(fn () => Arr::get(config('core.media.media.preview', []), "$this->type.type"));
    }

    protected function indirectUrl(): Attribute
    {
        return Attribute::get(function () {
            $id = static::isUsingStringId()
                ? $this->getKey()
                : dechex((int) $this->getKey());
            $hash = sha1($id);

            return route('media.indirect.url', compact('hash', 'id'));
        })->shouldCache();
    }

    public function canGenerateThumbnails(): bool
    {
        return (! $this->visibility || $this->visibility === 'public') && RvMedia::canGenerateThumbnails($this->mime_type);
    }

    public static function createName(string $name, int|string|null $folder): string
    {
        $index = 1;
        $baseName = $name;
        while (self::query()->where('name', $name)->where('folder_id', $folder)->withTrashed()->exists()) {
            $name = $baseName . '-' . $index++;
        }

        return $name;
    }

    public static function createSlug(string $name, string $extension, ?string $folderPath): string
    {
        if (setting('media_convert_file_name_to_uuid')) {
            return Str::uuid() . '.' . $extension;
        }

        if (setting('media_use_original_name_for_file_path')) {
            $slug = $name;
        } else {
            $slug = Str::slug($name, '-', ! RvMedia::turnOffAutomaticUrlTranslationIntoLatin() ? 'en' : false);
        }

        $index = 1;
        $baseSlug = $slug;
        while (File::exists(RvMedia::getRealPath(rtrim($folderPath, '/') . '/' . $slug . '.' . $extension))) {
            $slug = $baseSlug . '-' . $index++;
        }

        if (empty($slug)) {
            $slug = $slug . '-' . time();
        }

        return Str::limit($slug, end: '') . '.' . $extension;
    }
}
