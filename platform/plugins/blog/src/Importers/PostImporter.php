<?php

namespace Botble\Blog\Importers;

use Botble\ACL\Models\User;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Blog\Services\StoreCategoryService;
use Botble\Blog\Services\StoreTagService;
use Botble\Blog\Supports\PostFormat;
use Botble\DataSynchronize\Contracts\Importer\WithMapping;
use Botble\DataSynchronize\Importer\ImportColumn;
use Botble\DataSynchronize\Importer\Importer;
use Botble\Media\Facades\RvMedia;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostImporter extends Importer implements WithMapping
{
    public function chunkSize(): int
    {
        return 50;
    }

    public function getLabel(): string
    {
        return trans('plugins/blog::posts.posts');
    }

    public function columns(): array
    {
        $columns = [
            ImportColumn::make('name')
                ->rules(['required', 'string', 'max:250'], trans('plugins/blog::posts.import.rules.nullable_string_max', ['attribute' => 'Name', 'max' => 250])),
            ImportColumn::make('slug')
                ->rules(['nullable', 'string', 'max:250'], trans('plugins/blog::posts.import.rules.nullable_string_max', ['attribute' => 'Slug', 'max' => 250])),
            ImportColumn::make('description')
                ->rules(['nullable', 'string', 'max:400'], trans('plugins/blog::posts.import.rules.nullable_string_max', ['attribute' => 'Description', 'max' => 400])),
            ImportColumn::make('content')
                ->rules(['nullable', 'string', 'max:300000'], trans('plugins/blog::posts.import.rules.nullable_string_max', ['attribute' => 'Content', 'max' => '300,000'])),
            ImportColumn::make('tags')
                ->rules(['sometimes', 'array'], trans('plugins/blog::posts.import.rules.sometimes_array', ['attribute' => 'Tags'])),
            ImportColumn::make('categories')
                ->rules(['sometimes', 'array'], trans('plugins/blog::posts.import.rules.sometimes_array', ['attribute' => 'Categories'])),
            ImportColumn::make('status')
                ->rules([Rule::in(BaseStatusEnum::values())], trans('plugins/blog::posts.import.rules.in', ['attribute' => 'Status', 'values' => implode(', ', BaseStatusEnum::values())])),
            ImportColumn::make('is_featured')
                ->boolean()
                ->rules(['boolean'], trans('plugins/blog::posts.import.rules.in', ['attribute' => 'Is featured', 'values' => 'Yes, No'])),
            ImportColumn::make('image')
                ->rules(['nullable', 'string'], trans('plugins/blog::posts.import.rules.nullable_string', ['attribute' => 'Image'])),
        ];

        $postFormats = array_keys(PostFormat::getPostFormats(true));

        if (count($postFormats) > 1) {
            $columns[] = ImportColumn::make('format_type')
                ->rules(['nullable', 'string', 'max:50', Rule::in($postFormats)], trans('plugins/blog::posts.import.rules.nullable_string_max_in', ['attribute' => 'Format type', 'max' => 50, 'values' => implode(', ', $postFormats)]));
        } else {
            $columns[] = ImportColumn::make('format_type')
                ->rules(['nullable', 'string', 'max:50'], trans('plugins/blog::posts.import.rules.nullable_string_max', ['attribute' => 'Format type', 'max' => 50]));
        }

        return $columns;
    }

    public function examples(): array
    {
        $posts = Post::query()
            ->take(5)
            ->with(['categories', 'tags', 'slugable'])
            ->get()
            ->map(function (Post $post) { // @phpstan-ignore-line
                return [
                    ...$post->toArray(),
                    'slug' => $post->slugable->key,
                    'description' => Str::limit($post->description, 50),
                    'content' => Str::limit($post->content),
                    'tags' => $post->tags->pluck('name')->implode(', '),
                    'categories' => $post->categories->pluck('name')->implode(', '),
                    'is_featured' => rand(0, 1) ? 'Yes' : 'No',
                    'image' => RvMedia::getImageUrl($post->image),
                ];
            });

        if ($posts->isNotEmpty()) {
            return $posts->all();
        }

        return [
            [
                'name' => 'Exploring the Wonders of Machu Picchu',
                'slug' => 'exploring-the-wonders-of-machu-picchu',
                'description' => 'Embark on a journey through the ancient ruins of Machu Picchu, marveling at its breathtaking vistas and rich history.',
                'content' => 'Uncover the mysteries of the Incas as you traverse the rugged terrain and discover hidden temples nestled amidst the Andean peaks.',
                'tags' => 'travel, adventure, history, Peru',
                'categories' => 'Travel, History',
                'is_featured' => 'Yes',
                'image' => 'https://via.placeholder.com/600x400',
                'status' => BaseStatusEnum::PUBLISHED,
                'format_type' => '',
            ],
            [
                'name' => 'The Art of French Cuisine: A Culinary Adventure',
                'slug' => 'the-art-of-french-cuisine-a-culinary-adventure',
                'description' => 'Indulge your senses in the exquisite flavors of French cuisine, from delicate pastries to hearty stews, and discover the secrets of French culinary mastery.',
                'content' => 'Experience the charm of Parisian bistros and quaint countryside cafes as you sample delectable dishes prepared with the finest ingredients.',
                'tags' => 'food, cuisine, France, travel',
                'categories' => 'Food, Travel',
                'is_featured' => 'Yes',
                'image' => 'https://via.placeholder.com/600x400',
                'status' => BaseStatusEnum::PENDING,
                'format_type' => '',
            ],
            [
                'name' => 'Innovations in Renewable Energy: Shaping a Sustainable Future',
                'slug' => 'innovations-in-renewable-energy-shaping-a-sustainable-future',
                'description' => 'Explore the latest advancements in renewable energy technology and their role in mitigating climate change and fostering a greener planet for future generations.',
                'content' => 'From solar and wind power to hydroelectric and geothermal energy, learn how innovative solutions are revolutionizing the way we produce and consume electricity.',
                'tags' => 'renewable energy, sustainability, climate change, technology',
                'categories' => 'Science, Technology',
                'is_featured' => 'No',
                'image' => 'https://via.placeholder.com/600x400',
                'status' => BaseStatusEnum::DRAFT,
                'format_type' => '',
            ],
        ];
    }

    public function getValidateUrl(): string
    {
        return route('tools.data-synchronize.import.posts.validate');
    }

    public function getImportUrl(): string
    {
        return route('tools.data-synchronize.import.posts.store');
    }

    public function getDownloadExampleUrl(): ?string
    {
        return route('tools.data-synchronize.import.posts.download-example');
    }

    public function getExportUrl(): ?string
    {
        return Auth::user()->hasPermission('posts.export')
            ? route('tools.data-synchronize.export.posts.store')
            : null;
    }

    public function map(mixed $row): array
    {
        $tags = [];
        $categoryIds = [];

        if ($tagsStr = Arr::get($row, 'tags')) {
            $tags = str($tagsStr)
                ->explode(',')
                ->map(fn ($tag) => trim($tag))
                ->all();
        }

        if ($categories = Arr::get($row, 'categories')) {
            $categoryIds = $this->parseIdsFromString($categories, Category::class);
        }

        return [
            ...$row,
            'tags' => $tags,
            'categories' => $categoryIds,
        ];
    }

    public function handle(array $data): int
    {
        $storeCategoryService = new StoreCategoryService();
        $storeTagService = new StoreTagService();

        $count = 0;

        foreach ($data as $row) {
            $request = new Request();

            $request->merge([
                'categories' => Arr::pull($row, 'categories'),
                'tag' => Arr::pull($row, 'tags'),
            ]);

            $slug = Arr::pull($row, 'slug');

            /** @var Post $post */
            $post = Post::query()->firstOrCreate([
                'name' => Arr::pull($row, 'name'),
            ], [
                ...$row,
                'image' => $this->resolveMediaImage($row['image'] ?? null, 'posts'),
                'author_id' => Auth::id(),
                'author_type' => User::class,
            ]);

            if ($post->wasRecentlyCreated) {
                SlugHelper::createSlug($post, $slug);

                $count++;
            }

            $storeCategoryService->execute($request, $post);
            $storeTagService->execute($request, $post);
        }

        return $count;
    }

    protected function parseIdsFromString(string $items, string $modelClass): ?array
    {
        return str($items)
            ->explode(',')
            ->map(function ($item) use ($modelClass) {
                /**
                 * @var BaseModel $modelClass
                 * @var Post $model
                 */
                $model = $modelClass::query()->firstOrCreate(['name' => trim($item)]);

                if (SlugHelper::isSupportedModel($modelClass) && $model->wasRecentlyCreated) {
                    SlugHelper::createSlug($model);
                }

                return $model->getKey();
            })
            ->all();
    }
}
