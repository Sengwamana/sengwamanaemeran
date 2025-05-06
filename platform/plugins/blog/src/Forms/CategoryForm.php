<?php

namespace Botble\Blog\Forms;

use Botble\Base\Forms\FieldOptions\CoreIconFieldOption;
use Botble\Base\Forms\FieldOptions\DescriptionFieldOption;
use Botble\Base\Forms\FieldOptions\HiddenFieldOption;
use Botble\Base\Forms\FieldOptions\IsDefaultFieldOption;
use Botble\Base\Forms\FieldOptions\IsFeaturedFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\Fields\CoreIconField;
use Botble\Base\Forms\Fields\HiddenField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Blog\Http\Requests\CategoryRequest;
use Botble\Blog\Models\Category;

class CategoryForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Category::class)
            ->setValidatorClass(CategoryRequest::class)
            ->add(
                'order',
                HiddenField::class,
                HiddenFieldOption::make()
                    ->value(function () {
                        if ($this->getModel()->exists) {
                            return $this->getModel()->order;
                        }

                        return Category::query()
                                ->whereIn('parent_id', [0, null])
                                ->latest('order')
                                ->value('order') + 1;
                    })
            )
            ->add('name', TextField::class, NameFieldOption::make()->required())
            ->add(
                'parent_id',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('core/base::forms.parent'))
                    ->choices(function () {
                        $modelId = null;

                        if ($this->getModel() && $this->getModel()->exists) {
                            $modelId = $this->getModel()->getKey();
                        }

                        $categories = [];
                        foreach (get_categories(['condition' => []]) as $row) {
                            if ($modelId && ($modelId === $row->id || $modelId === $row->parent_id)) {
                                continue;
                            }

                            $categories[$row->id] = $row->indent_text . ' ' . $row->name;
                        }

                        return [0 => trans('plugins/blog::categories.none')] + $categories;
                    })
                    ->searchable()
            )
            ->add('description', TextareaField::class, DescriptionFieldOption::make())
            ->add('is_default', OnOffField::class, IsDefaultFieldOption::make())
            ->add(
                'icon',
                CoreIconField::class,
                CoreIconFieldOption::make()
            )
            ->add(
                'is_featured',
                OnOffField::class,
                IsFeaturedFieldOption::make()
            )
            ->add('status', SelectField::class, StatusFieldOption::make())
            ->setBreakFieldPoint('status');
    }
}
