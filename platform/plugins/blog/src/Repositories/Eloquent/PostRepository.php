<?php

namespace Botble\Blog\Repositories\Eloquent;

use Botble\Base\Models\BaseQueryBuilder;
use Botble\Blog\Models\Post;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Language\Facades\Language;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class PostRepository extends RepositoriesAbstract implements PostInterface
{
    public function getFeatured(int $limit = 5, array $with = []): Collection
    {
        $data = $this->model
            ->wherePublished()
            ->where('is_featured', true)
            ->limit($limit)
            ->with(array_merge(['slugable'], $with))
            ->orderByDesc('created_at');

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function getListPostNonInList(array $selected = [], int $limit = 7, array $with = []): Collection
    {
        $data = $this->model
            ->wherePublished()
            ->whereNotIn('id', $selected)
            ->limit($limit)
            ->with($with)
            ->orderByDesc('created_at');

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function getRelated(int|string $id, int $limit = 3): Collection
    {
        /**
         * @var Post $model
         */
        $model = $this->model;

        $data = $model
            ->wherePublished()
            ->where('id', '!=', $id)
            ->limit($limit)
            ->with('slugable')
            ->orderByDesc('created_at')
            ->whereHas('categories', function (Builder $query) use ($id): void {
                $query->whereIn('categories.id', $this->getRelatedCategoryIds($id));
            });

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function getRelatedCategoryIds(Post|int|string $model): array
    {
        $model = $model instanceof Post ? $model : $this->findById($model);

        if (! $model) {
            return [];
        }

        try {
            return $model->categories()->allRelatedIds()->toArray();
        } catch (Exception) {
            return [];
        }
    }

    public function getByCategory(
        array|int|string $categoryId,
        int $paginate = 12,
        int $limit = 0
    ): Collection|LengthAwarePaginator {
        $data = $this->model
            ->wherePublished()
            ->whereHas('categories', function (Builder $query) use ($categoryId): void {
                $query->whereIn('categories.id', array_filter((array) $categoryId));
            })
            ->select('*')
            ->distinct()
            ->with('slugable')
            ->orderByDesc('created_at');

        if ($paginate != 0) {
            return $this->applyBeforeExecuteQuery($data)->paginate($paginate);
        }

        return $this->applyBeforeExecuteQuery($data)->limit($limit)->get();
    }

    public function getByUserId(int|string $authorId, int $paginate = 6): Collection|LengthAwarePaginator
    {
        $data = $this->model
            ->wherePublished()
            ->where('author_id', $authorId)
            ->with('slugable')
            ->orderByDesc('created_at');

        return $this->applyBeforeExecuteQuery($data)->paginate($paginate);
    }

    public function getDataSiteMap(): Collection|LengthAwarePaginator
    {
        $data = $this->model
            ->wherePublished()
            ->with('slugable')
            ->orderByDesc('created_at');

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function getByTag(int|string $tag, int $paginate = 12): Collection|LengthAwarePaginator
    {
        $data = $this->model
            ->with(['slugable', 'categories', 'categories.slugable', 'author'])
            ->wherePublished()
            ->whereHas('tags', function (Builder $query) use ($tag): void {
                $query->where('tags.id', $tag);
            })
            ->orderByDesc('created_at');

        return $this->applyBeforeExecuteQuery($data)->paginate($paginate);
    }

    public function getRecentPosts(int $limit = 5, int|string $categoryId = 0): Collection
    {
        $data = $this->model->wherePublished();

        if ($categoryId != 0) {
            $data = $data
                ->whereHas('categories', function (Builder $query) use ($categoryId): void {
                    $query->where('categories.id', $categoryId);
                });
        }

        $data = $data->limit($limit)
            ->with('slugable')
            ->select('*')
            ->orderByDesc('created_at');

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function getSearch(
        ?string $keyword,
        int $limit = 10,
        int $paginate = 10
    ): Collection|LengthAwarePaginator {
        $data = $this->model
            ->with('slugable')
            ->wherePublished()
            ->orderByDesc('created_at');

        $data = $this->search($data, $keyword);

        if ($limit) {
            $data = $data->limit($limit);
        }

        if ($paginate) {
            return $this->applyBeforeExecuteQuery($data)->paginate($paginate);
        }

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function getAllPosts(
        int $perPage = 12,
        bool $active = true,
        array $with = ['slugable']
    ): Collection|LengthAwarePaginator {
        $data = $this->model
            ->with($with)
            ->orderByDesc('created_at');

        if ($active) {
            $data = $data->wherePublished();
        }

        return $this->applyBeforeExecuteQuery($data)->paginate($perPage);
    }

    public function getPopularPosts(int $limit, array $args = []): Collection
    {
        $data = $this->model
            ->with('slugable')
            ->orderByDesc('views')
            ->wherePublished()
            ->limit($limit);

        if (! empty(Arr::get($args, 'where'))) {
            $data = $data->where($args['where']);
        }

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function getFilters(array $filters): Collection|LengthAwarePaginator
    {
        $data = $this->originalModel;

        if ($filters['categories'] !== null) {
            $categories = array_filter((array) $filters['categories']);

            $data = $data->whereHas('categories', function (Builder $query) use ($categories): void {
                $query->whereIn('categories.id', $categories);
            });
        }

        if ($filters['categories_exclude'] !== null) {
            $data = $data
                ->whereHas('categories', function (Builder $query) use ($filters): void {
                    $query->whereNotIn('categories.id', array_filter((array) $filters['categories_exclude']));
                });
        }

        if ($filters['exclude'] !== null) {
            $data = $data->whereNotIn('id', array_filter((array) $filters['exclude']));
        }

        if ($filters['include'] !== null) {
            $data = $data->whereNotIn('id', array_filter((array) $filters['include']));
        }

        if ($filters['author'] !== null) {
            $data = $data->whereIn('author_id', array_filter((array) $filters['author']));
        }

        if ($filters['author_exclude'] !== null) {
            $data = $data->whereNotIn('author_id', array_filter((array) $filters['author_exclude']));
        }

        if ($filters['featured'] !== null) {
            $data = $data->where('is_featured', $filters['featured']);
        }

        if ($filters['search'] !== null) {
            $data = $this->search($data, $filters['search']);
        }

        $orderBy = Arr::get($filters, 'order_by', 'updated_at');
        $order = Arr::get($filters, 'order', 'desc');

        $data = $data
            ->wherePublished()
            ->orderBy($orderBy, $order);

        return $this->applyBeforeExecuteQuery($data)->paginate((int) $filters['per_page']);
    }

    protected function search(BaseQueryBuilder|Builder $model, ?string $keyword): BaseQueryBuilder|Builder
    {
        if (! $model instanceof BaseQueryBuilder || ! $keyword) {
            return $model;
        }

        if (
            is_plugin_active('language') &&
            is_plugin_active('language-advanced') &&
            Language::getCurrentLocale() != Language::getDefaultLocale()
        ) {
            return $model
                ->whereHas('translations', function (BaseQueryBuilder $query) use ($keyword): void {
                    $query
                        ->addSearch('name', $keyword, false, false)
                        ->addSearch('description', $keyword, false);
                });
        }

        return $model
            ->where(function (BaseQueryBuilder $query) use ($keyword): void {
                $query
                    ->addSearch('name', $keyword, false, false)
                    ->addSearch('description', $keyword, false);
            });
    }
}
