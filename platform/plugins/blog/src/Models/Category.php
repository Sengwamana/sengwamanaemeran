<?php

namespace Botble\Blog\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Contracts\HasTreeCategory as HasTreeCategoryContract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\Html;
use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\HasTreeCategory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class Category extends BaseModel implements HasTreeCategoryContract
{
    use HasTreeCategory;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'icon',
        'is_featured',
        'order',
        'is_default',
        'status',
        'author_id',
        'author_type',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
        'description' => SafeContent::class,
        'is_default' => 'bool',
        'order' => 'int',
    ];

    protected static function booted(): void
    {
        static::deleted(function (Category $category): void {
            $category->children()->each(fn (Model $child) => $child->delete());

            $category->posts()->detach();
        });
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_categories')->with('slugable');
    }

    public function parent(): BelongsTo
    {
        return $this
            ->belongsTo(Category::class, 'parent_id')
            ->whereNot('parent_id', $this->getKey())
            ->withDefault();
    }

    public function children(): HasMany
    {
        return $this
            ->hasMany(Category::class, 'parent_id')
            ->whereNot('id', $this->getKey());
    }

    public function activeChildren(): HasMany
    {
        return $this
            ->children()
            ->wherePublished()
            ->with(['slugable', 'activeChildren']);
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

    public static function getChildrenIds(EloquentCollection $children, $categoryIds = []): array
    {
        if ($children->isEmpty()) {
            return $categoryIds;
        }

        foreach ($children as $item) {
            $categoryIds[] = $item->id;
            if ($item->children->isNotEmpty()) {
                $categoryIds = static::getChildrenIds($item->activeChildren, $categoryIds);
            }
        }

        return $categoryIds;
    }

    protected function badgeWithCount(): Attribute
    {
        return Attribute::get(function (): HtmlString {
            $categoryIds = static::getChildrenIds($this->activeChildren);

            if (empty($categoryIds)) {
                $categoryIds = [$this->getKey()];
            }

            $postsCount = DB::table('post_categories')
                ->whereIn('category_id', $categoryIds)
                ->distinct('post_id')
                ->count();

            return Html::link($this->url, sprintf('(%s)', $postsCount), [
                'data-bs-toggle' => 'tooltip',
                'data-bs-original-title' => trans('plugins/blog::categories.total_posts', ['total' => $postsCount]),
                'target' => '_blank',
            ]);
        })->shouldCache();
    }
}
