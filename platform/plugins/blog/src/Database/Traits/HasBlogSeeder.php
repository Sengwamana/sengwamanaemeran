<?php

namespace Botble\Blog\Database\Traits;

use Botble\ACL\Models\User;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Blog\Models\Tag;
use Botble\Setting\Facades\Setting;
use Botble\Slug\Facades\SlugHelper;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait HasBlogSeeder
{
    protected Collection $userIds;

    protected function getUserIds(): Collection
    {
        if (! isset($this->userIds)) {
            $this->userIds = User::query()->pluck('id');
        }

        return $this->userIds;
    }

    protected function createBlogCategories(array $categories, bool $truncate = true): void
    {
        if ($truncate) {
            Category::query()->truncate();
        }

        $faker = $this->fake();

        foreach ($categories as $index => $item) {
            $item['description'] ??= $faker->text();
            $item['is_featured'] ??= ! isset($item['parent_id']) && $index != 0;
            $item['parent_id'] ??= 0;

            $category = $this->createBlogCategory(Arr::except($item, 'children'));

            if (Arr::has($item, 'children')) {
                foreach (Arr::get($item, 'children', []) as $child) {
                    $child['parent_id'] = $category->getKey();

                    $this->createBlogCategory($child);
                }
            }

            $this->createMetadata($category, $item);
        }
    }

    protected function createBlogTags(array $tags, bool $truncate = true): void
    {
        if ($truncate) {
            Tag::query()->truncate();
        }

        $userIds = $this->getUserIds();

        foreach ($tags as $item) {
            $item['author_id'] ??= $userIds->random();
            $item['author_type'] ??= User::class;

            /**
             * @var Tag $tag
             */
            $tag = Tag::query()->create(Arr::except($item, ['metadata']));

            SlugHelper::createSlug($tag);

            $this->createMetadata($tag, $item);
        }
    }

    protected function createBlogPosts(array $posts, bool $truncate = true): void
    {
        if ($truncate) {
            Post::query()->truncate();
            DB::table('post_categories')->truncate();
            DB::table('post_tags')->truncate();
        }

        $faker = $this->fake();

        $categoryIds = Category::query()->pluck('id');
        $tagIds = Tag::query()->pluck('id');
        $userIds = $this->getUserIds();

        foreach ($posts as $item) {
            $item['views'] ??= $faker->numberBetween(100, 2500);
            $item['description'] ??= $faker->realText();
            $item['is_featured'] ??= $faker->boolean();

            if (! empty($item['content'])) {
                $item['content'] = $this->removeBaseUrlFromString((string) $item['content']);
            } else {
                $item['content'] = $faker->realText();
            }

            $item['author_id'] ??= $userIds->random();
            $item['author_type'] ??= User::class;

            /**
             * @var Post $post
             */
            $post = Post::query()->create(Arr::except($item, ['metadata']));

            $post->categories()->sync(array_unique([
                $categoryIds->random(),
                $categoryIds->random(),
            ]));

            $post->tags()->sync(array_unique([
                $tagIds->random(),
                $tagIds->random(),
                $tagIds->random(),
            ]));

            SlugHelper::createSlug($post);

            $this->createMetadata($post, $item);
        }
    }

    protected function getCategoryId(string $name): int|string
    {
        return Category::query()->where('name', $name)->value('id');
    }

    protected function createBlogCategory(array $item): Category
    {
        $userIds = $this->getUserIds();

        $item['author_id'] ??= $userIds->random();
        $item['author_type'] ??= User::class;

        /**
         * @var Category $category
         */
        $category = Category::query()->create(Arr::except($item, ['metadata']));

        SlugHelper::createSlug($category);

        $this->createMetadata($category, $item);

        return $category;
    }

    public function setPostSlugPrefix(string $prefix = 'blog'): void
    {
        Setting::set([
            SlugHelper::getPermalinkSettingKey(Post::class) => $prefix,
            SlugHelper::getPermalinkSettingKey(Category::class) => $prefix,
        ]);

        Setting::save();

        Slug::query()->where('reference_type', Post::class)->update(['prefix' => $prefix]);
        Slug::query()->where('reference_type', Category::class)->update(['prefix' => $prefix]);
    }
}
