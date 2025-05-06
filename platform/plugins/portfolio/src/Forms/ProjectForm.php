<?php

namespace Botble\Portfolio\Forms;

use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\Base\Forms\FieldOptions\DescriptionFieldOption;
use Botble\Base\Forms\FieldOptions\IsFeaturedFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\SortOrderFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Portfolio\Http\Requests\ProjectRequest;
use Botble\Portfolio\Models\Project;

class ProjectForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->setupModel(new Project())
            ->setValidatorClass(ProjectRequest::class)
            ->add('name', TextField::class, NameFieldOption::make()->required()->toArray())
            ->add('description', TextareaField::class, DescriptionFieldOption::make()->toArray())
            ->add('content', EditorField::class, ContentFieldOption::make()->allowedShortcodes()->toArray())
            ->add('status', SelectField::class, StatusFieldOption::make()->toArray())
            ->add(
                'is_featured',
                OnOffField::class,
                IsFeaturedFieldOption::make()
            )
            ->add('image', 'mediaImage', [
                'label' => trans('plugins/portfolio::portfolio.image'),
            ])
            ->add('author', 'text', [
                'label' => trans('plugins/portfolio::portfolio.project.author'),
                'attr' => [
                    'placeholder' => trans('plugins/portfolio::portfolio.project.author'),
                ],
            ])
            ->add('place', 'text', [
                'label' => trans('plugins/portfolio::portfolio.project.place'),
                'attr' => [
                    'placeholder' => trans('plugins/portfolio::portfolio.project.place'),
                ],
            ])
            ->add('client', 'text', [
                'label' => trans('plugins/portfolio::portfolio.project.client'),
                'attr' => [
                    'placeholder' => trans('plugins/portfolio::portfolio.project.client'),
                ],
            ])
            ->add('start_date', 'datePicker', [
                'label' => trans('plugins/portfolio::portfolio.project.start_date'),
                'attr' => [
                    'placeholder' => trans('plugins/portfolio::portfolio.project.start_date'),
                ],
            ])
            ->add('order', NumberField::class, SortOrderFieldOption::make())
            ->setBreakFieldPoint('status');
    }
}
