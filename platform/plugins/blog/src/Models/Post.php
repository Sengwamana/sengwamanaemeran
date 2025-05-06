<?php

namespace Botble\Blog\Models;

use Botble\ACL\Models\User;
use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\Blog\Enums\PostStatusEnum;
use Botble\Revision\RevisionableTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Post extends BaseModel
{
    use RevisionableTrait;

    protected $table = 'posts';

    protected bool $revisionEnabled = true;

    protected bool $revisionCleanup = true;

    protected int $historyLimit = 20;

    protected array $dontKeepRevisionOf = [
        'content',
        'views',
    ];

    protected $fillable = [
        'name',
        'description',
        'content',
        'image',
        'is_featured',
        'format_type',
        'status',
        'author_id',
        'author_type',
    ];

    protected static function booted(): void
    {
        static::deleted(function (self $post): void {
            $post->categories()->detach();
            $post->tags()->detach();
        });

        static::creating(function (self $post): void {
            $post->author_id = $post->author_id ?: auth()->id();
            $post->author_type = $post->author_type ?: User::class;
        });
    }

    protected $casts = [
        'status' => PostStatusEnum::class,
        'name' => SafeContent::class,
        'description' => SafeContent::class,
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'post_categories');
    }

    public function author(): MorphTo
    {
        return $this->morphTo()->withDefault();
    }

    protected function firstCategory(): Attribute
    {
        return Attribute::get(function (): ?Category {
            $this->loadMissing('categories');

            return $this->categories->first();
        });
    }

    protected function timeReading(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if (! $this->content) {
                    return null;
                }

                $this->loadMissing('metadata');

                $timeToRead = $this->getMetaData('time_to_read', true);

                if ($timeToRead != null) {
                    return number_format((float) $timeToRead);
                }

                return number_format(ceil(str_word_count(strip_tags($this->content)) / 200));
            }
        );
    }

    protected function authorUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if (! $this->author_id || ! class_exists($this->author_type)) {
                    return null;
                }

                /**
                 * @var BaseModel $author
                 */
                $author = $this->author;

                if ($author && method_exists($author, 'url')) {
                    return $author->url;
                }

                return null;
            }
        );
    }

    protected function authorName(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if (! $this->author_id || ! class_exists($this->author_type)) {
                    return null;
                }

                return $this->author?->name;
            }
        );
    }
}
