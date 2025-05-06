<?php

namespace Botble\Blog\Widgets\Fronts;

use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Widget\AbstractWidget;
use Botble\Widget\Forms\WidgetForm;
use Illuminate\Support\Collection;

class Tags extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Tags'),
            'description' => __('Popular tags'),
            'number_display' => 5,
        ]);
    }

    protected function data(): array|Collection
    {
        return [
            'tags' => get_popular_tags((int) $this->getConfig('number_display')),
        ];
    }

    protected function settingForm(): WidgetForm|string|null
    {
        return WidgetForm::createFromArray($this->getConfig())
            ->add('name', TextField::class, NameFieldOption::make())
            ->add(
                'number_display',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(__('Number tags to display'))
            );
    }

    protected function requiredPlugins(): array
    {
        return ['blog'];
    }
}
