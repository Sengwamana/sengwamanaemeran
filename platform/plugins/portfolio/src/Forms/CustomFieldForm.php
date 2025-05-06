<?php

namespace Botble\Portfolio\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Portfolio\Enums\CustomFieldType;
use Botble\Portfolio\Http\Requests\CustomFieldRequest;
use Botble\Portfolio\Models\CustomField;

class CustomFieldForm extends FormAbstract
{
    public function setup(): void
    {
        Assets::addScripts('jquery-ui')
            ->addScriptsDirectly('vendor/core/plugins/portfolio/js/portfolio.js');

        $this
            ->setupModel(new CustomField())
            ->setValidatorClass(CustomFieldRequest::class)
            ->add('name', TextField::class, NameFieldOption::make()->required()->toArray())
            ->add('required', 'onOff', [
                'label' => trans('plugins/portfolio::portfolio.custom_field.required'),
            ])
            ->add('placeholder', 'text', [
                'label' => trans('plugins/portfolio::portfolio.custom_field.placeholder'),
                'attr' => [
                    'placeholder' => trans('plugins/portfolio::portfolio.custom_field.placeholder_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('type', 'customSelect', [
                'label' => trans('plugins/portfolio::portfolio.custom_field.type'),
                'required' => true,
                'attr' => ['class' => 'form-control custom-field-type'],
                'choices' => CustomFieldType::labels(),
            ])
            ->setBreakFieldPoint('type')
            ->addMetaBoxes([
                'custom_fields_box' => [
                    'attributes' => [
                        'id' => 'custom_fields_box',
                    ],
                    'id' => 'custom_fields_box',
                    'title' => trans('plugins/portfolio::portfolio.custom_field.options'),
                    'content' => view(
                        'plugins/portfolio::custom-fields.options',
                        ['options' => $this->getModel()->options->sortBy('order')]
                    )->render(),
                ],
            ]);
    }
}
