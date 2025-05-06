<?php

namespace Botble\Blog\Widgets\Fronts;

use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Blog\Models\Category;
use Botble\Widget\AbstractWidget;
use Botble\Widget\Forms\WidgetForm;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Categories extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Blog Categories'),
            'description' => __('Widget display blog categories'),
            'display_posts_count' => 'yes',
            'category_ids' => [],
        ]);
    }

    protected function data(): array|Collection
    {
        $config = $this->getConfig();

        $categoryIds = Arr::get($config, 'category_ids', []);

        $categories = Category::query()
            ->select(['id', 'name'])
            ->with('slugable')
            ->when($config['display_posts_count'], function ($query) {
                return $query->withCount('posts');
            })
            ->wherePublished()
            ->when($categoryIds, function ($query) use ($categoryIds) {
                return $query->whereIn('id', $categoryIds);
            }, function ($query) {
                return $query
                    ->take(5)
                    ->where(fn ($query) => $query->whereNull('parent_id')->orWhere('parent_id', 0));
            })
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->get();

        return compact('categories');
    }

    protected function settingForm(): WidgetForm|string|null
    {
        $data = $this->getConfig();

        $categories = Category::query()->pluck('name', 'id')->all();
        $categoryIds = Arr::get($data, 'category_ids', []);

        if (! is_array($categoryIds)) {
            $categoryIds = $categoryIds ? explode(',', $categoryIds) : null;
        }

        return WidgetForm::createFromArray($data)
            ->add('name', TextField::class, NameFieldOption::make())
            ->add(
                'category_ids',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Choose categories'))
                    ->choices($categories)
                    ->selected($categoryIds)
                    ->searchable()
                    ->multiple()
            )
            ->add(
                'display_posts_count',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Display posts count?'))
                    ->choices([
                        'yes' => __('Yes'),
                        'no' => __('No'),
                    ])
                    ->selected($data['display_posts_count'])
            );
    }

    protected function requiredPlugins(): array
    {
        return ['blog'];
    }
}
