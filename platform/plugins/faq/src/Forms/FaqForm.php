<?php

namespace Botble\Faq\Forms;

use Botble\Base\Forms\FieldOptions\EditorFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\FormAbstract;
use Botble\Faq\Http\Requests\FaqRequest;
use Botble\Faq\Models\Faq;
use Botble\Faq\Models\FaqCategory;

class FaqForm extends FormAbstract
{
    public function setup(): void
    {
        $faqCategories = FaqCategory::query()
            ->pluck(
                'name',
                'id'
            )
            ->all();

        $this
            ->model(Faq::class)
            ->setValidatorClass(FaqRequest::class)
            ->add(
                'category_id',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/faq::faq.category'))
                    ->choices(['' => trans('plugins/faq::faq.select_category')] + $faqCategories)
                    ->required()
            )
            ->add(
                'question',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(trans('plugins/faq::faq.question'))
                    ->required()
                    ->rows(4)
            )
            ->add(
                'answer',
                EditorField::class,
                EditorFieldOption::make()->label(trans('plugins/faq::faq.answer'))->required()->rows(4)
            )
            ->add('status', SelectField::class, StatusFieldOption::make())
            ->setBreakFieldPoint('status');
    }
}
