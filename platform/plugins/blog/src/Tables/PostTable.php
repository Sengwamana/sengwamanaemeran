<?php

namespace Botble\Blog\Tables;

use Botble\Base\Facades\Html;
use Botble\Base\Models\BaseQueryBuilder;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\SelectBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\ImageColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;

class PostTable extends TableAbstract
{
    public function setup(): void
    {
        $this->defaultSortColumnName = 'created_at';

        $this
            ->model(Post::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('posts.create'))
            ->addActions([
                EditAction::make()->route('posts.edit'),
                DeleteAction::make()->route('posts.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                ImageColumn::make(),
                NameColumn::make()->route('posts.edit'),
                FormattedColumn::make('categories_name')
                    ->title(trans('plugins/blog::posts.categories'))
                    ->width(150)
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(function (FormattedColumn $column) {
                        $categories = $column
                            ->getItem()
                            ->categories
                            ->sortBy('name')
                            ->map(function (Category $category) {
                                return Html::link(route('categories.edit', $category->getKey()), $category->name, ['target' => '_blank']);
                            })
                            ->all();

                        return implode(', ', $categories);
                    })
                    ->withEmptyState(),
                FormattedColumn::make('author_id')
                    ->title(trans('plugins/blog::posts.author'))
                    ->width(150)
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(function (FormattedColumn $column) {
                        return $column->getItem()->author_name;
                    })
                    ->renderUsing(function (FormattedColumn $column) {
                        $url = $column->getItem()->author_url;

                        if (! $url) {
                            return null;
                        }

                        return Html::link($url, $column->getItem()->author_name, ['target' => '_blank']);
                    })
                    ->withEmptyState(),
                CreatedAtColumn::make(),
                StatusColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('posts.destroy'),
            ])
            ->addBulkChanges([
                NameBulkChange::make(),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
                SelectBulkChange::make()
                    ->name('category')
                    ->title(trans('plugins/blog::posts.category'))
                    ->searchable()
                    ->choices(fn () => Category::query()->pluck('name', 'id')->all()),
            ])
            ->queryUsing(function (Builder $query) {
                return $query
                    ->with([
                        'categories' => function (BelongsToMany $query): void {
                            $query->select(['categories.id', 'categories.name']);
                        },
                        'author',
                    ])
                    ->select([
                        'id',
                        'name',
                        'image',
                        'created_at',
                        'status',
                        'updated_at',
                        'author_id',
                        'author_type',
                    ]);
            })
            ->onAjax(function (self $table) {
                return $table->toJson(
                    $table
                        ->table
                        ->eloquent($table->query())
                        ->filter(function ($query) {
                            if ($keyword = $this->request->input('search.value')) {
                                $keyword = '%' . $keyword . '%';

                                return $query
                                    ->where('name', 'LIKE', $keyword)
                                    ->orWhereHas('categories', function ($subQuery) use ($keyword) {
                                        return $subQuery
                                            ->where('name', 'LIKE', $keyword);
                                    })
                                    ->orWhereHas('author', function ($subQuery) use ($keyword) {
                                        return $subQuery
                                            ->where('first_name', 'LIKE', $keyword)
                                            ->orWhere('last_name', 'LIKE', $keyword)
                                            ->orWhereRaw('concat(first_name, " ", last_name) LIKE ?', $keyword);
                                    });
                            }

                            return $query;
                        })
                );
            })
            ->onFilterQuery(
                function (
                    EloquentBuilder|QueryBuilder|EloquentRelation $query,
                    string $key,
                    string $operator,
                    ?string $value
                ) {
                    if (! $value || $key !== 'category') {
                        return false;
                    }

                    return $query->whereHas(
                        'categories',
                        fn (BaseQueryBuilder $query) => $query->where('categories.id', $value)
                    );
                }
            )
            ->onSavingBulkChangeItem(function (Post $item, string $inputKey, ?string $inputValue) {
                if ($inputKey !== 'category') {
                    return null;
                }

                $item->categories()->sync([$inputValue]);

                return $item;
            });
    }
}
