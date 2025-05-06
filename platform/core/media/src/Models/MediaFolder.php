<?php

namespace Botble\Media\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\Media\Facades\RvMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaFolder extends BaseModel
{
    use SoftDeletes;

    protected $table = 'media_folders';

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'user_id',
        'color',
    ];

    protected $casts = [
        'name' => SafeContent::class,
    ];

    protected static function booted(): void
    {
        static::deleted(function (MediaFolder $folder): void {
            if ($folder->isForceDeleting()) {
                $folder->files()->withTrashed()->each(fn (MediaFile $file) => $file->forceDelete());

                if (Storage::directoryExists($folder->slug)) {
                    Storage::deleteDirectory($folder->slug);
                }
            } else {
                $folder->files()->withTrashed()->each(fn (MediaFile $file) => $file->delete());
            }
        });

        static::restoring(function (MediaFolder $folder): void {
            $folder->files()->each(fn (MediaFile $file) => $file->restore());
        });

        static::addGlobalScope('ownMedia', function (Builder $query): void {
            if (RvMedia::canOnlyViewOwnMedia()) {
                $query->where('media_folders.user_id', auth()->id());
            }
        });
    }

    public function files(): HasMany
    {
        return $this->hasMany(MediaFile::class, 'folder_id', 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'parent_id')->withDefault();
    }

    protected function parents(): Attribute
    {
        return Attribute::get(function (): Collection {
            $parents = collect();

            $parent = $this->parent;

            while ($parent->id) {
                $parents->push($parent);
                $parent = $parent->parent;
            }

            return $parents;
        });
    }

    public static function getFullPath(int|string|null $folderId, ?string $path = ''): ?string
    {
        if (! $folderId) {
            return $path;
        }

        $folder = self::query()->where('id', $folderId)->withTrashed()->first();

        if (empty($folder)) {
            return $path;
        }

        $parent = self::getFullPath($folder->parent_id, $path);

        if (! $parent) {
            return $folder->slug;
        }

        return rtrim($parent, '/') . '/' . $folder->slug;
    }

    public static function createSlug(string $name, int|string|null $parentId): string
    {
        $slug = Str::slug($name, '-', ! RvMedia::turnOffAutomaticUrlTranslationIntoLatin() ? 'en' : false);
        $index = 1;
        $baseSlug = $slug;
        while (self::query()->where('slug', $slug)->where('parent_id', $parentId)->withTrashed()->exists()) {
            $slug = $baseSlug . '-' . $index++;
        }

        return $slug;
    }

    public static function createName(string $name, int|string|null $parentId): string
    {
        $newName = $name;
        $index = 1;
        $baseSlug = $newName;
        while (self::query()->where('name', $newName)->where('parent_id', $parentId)->withTrashed()->exists()) {
            $newName = $baseSlug . '-' . $index++;
        }

        return $newName;
    }
}
