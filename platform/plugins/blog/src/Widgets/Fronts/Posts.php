<?php

namespace Botble\Blog\Widgets\Fronts;

use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Widget\AbstractWidget;
use Botble\Widget\Forms\WidgetForm;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Posts extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Blog Posts'),
            'description' => __('Display blog posts'),
            'number_display' => 3,
            'type' => '',
        ]);
    }

    public function data(): array|Collection
    {
        $config = $this->getConfig();

        $limit = (int) Arr::get($config, 'number_display', Arr::get($config, 'limit', 3));

        $posts = match (Arr::get($config, 'type')) {
            'featured' => get_featured_posts($limit),
            'popular' => get_popular_posts($limit),
            'recent' => get_recent_posts($limit),
            default => get_latest_posts($limit),
        };

        return compact('posts');
    }

    protected function settingForm(): WidgetForm|string|null
    {
        return WidgetForm::createFromArray($this->getConfig())
            ->add('name', TextField::class, TextFieldOption::make()->label(__('Name')))
            ->add(
                'type',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Type'))
                    ->choices([
                        '' => __('Latest'),
                        'featured' => __('Featured'),
                        'popular' => __('Popular'),
                        'recent' => __('Recent'),
                    ])
            )
            ->add('number_display', NumberField::class, NumberFieldOption::make()->label(__('Limit')));
    }

    protected function requiredPlugins(): array
    {
        return ['blog'];
    }
}
