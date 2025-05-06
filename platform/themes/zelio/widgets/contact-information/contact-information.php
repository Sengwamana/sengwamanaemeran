<?php

use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\RepeaterFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\RepeaterField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Widget\AbstractWidget;
use Botble\Widget\Forms\WidgetForm;
use Illuminate\Support\Collection;

class ContactInformationWidget extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Contact Information'),
            'description' => __('Displays contact details such as phone number, email, and address.'),
            'bio' => null,
            'details' => [],
            'display_social_links' => false,
        ]);
    }

    protected function settingForm(): WidgetForm|string|null
    {
        return WidgetForm::createFromArray($this->getConfig())
            ->add(
                'bio',
                TextareaField::class,
                TextareaFieldOption::make()->label(__('Bio'))
                    ->helperText(__('Write a short bio about yourself.'))
                    ->helperText(__('This field only visible on Sidebar Panel.'))
            )
            ->add(
                'image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Image'))
                    ->helperText(__('This field only visible on Primary Sidebar.'))
            )
            ->add(
                'signature',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Signature'))
                    ->helperText(__('This field only visible on Primary Sidebar.'))
            )
            ->add(
                'details',
                RepeaterField::class,
                RepeaterFieldOption::make()
                    ->fields([
                        [
                            'type' => 'text',
                            'label' => __('Label'),
                            'attributes' => [
                                'name' => 'label',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                        ],
                        [
                            'type' => 'text',
                            'label' => __('Value'),
                            'attributes' => [
                                'name' => 'value',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                        ],
                        [
                            'type' => 'coreIcon',
                            'label' => __('Icon'),
                            'attributes' => [
                                'name' => 'icon',
                                'value' => null,
                            ],
                        ],
                        [
                            'type' => 'text',
                            'label' => __('URL'),
                            'attributes' => [
                                'name' => 'url',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                        ],
                    ])
            );
    }

    protected function data(): array|Collection
    {
        return [
            'details' => collect($this->getConfig('details'))
                ->reject(fn ($item) => $item === '[]')
                ->map(fn ($item) => collect($item)->pluck('value', 'key')->all())
                ->all(),
        ];
    }
}
