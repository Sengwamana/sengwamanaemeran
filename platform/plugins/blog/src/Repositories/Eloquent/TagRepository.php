<?php

namespace Botble\Blog\Repositories\Eloquent;

use Botble\Blog\Repositories\Interfaces\TagInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Support\Collection;

class TagRepository extends RepositoriesAbstract implements TagInterface
{
    public function getDataSiteMap(): Collection
    {
        $data = $this->model
            ->with('slugable')
            ->wherePublished()
            ->orderByDesc('created_at')
            ->select(['id', 'name', 'updated_at']);

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function getPopularTags(int $limit, array $with = ['slugable'], array $withCount = ['posts']): Collection
    {
        $data = $this->model
            ->with($with)
            ->withCount($withCount)
            ->orderByDesc('posts_count')
            ->limit($limit);

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function getAllTags($active = true): Collection
    {
        $data = $this->model;
        if ($active) {
            $data = $data->wherePublished();
        }

        return $this->applyBeforeExecuteQuery($data)->get();
    }
}
