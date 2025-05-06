<?php

namespace ArchiElite\Announcement\Forms;

use ArchiElite\Announcement\Http\Requests\AnnouncementRequest;
use ArchiElite\Announcement\Models\Announcement;
use Botble\Base\Forms\FieldOptions\DatePickerFieldOption;
use Botble\Base\Forms\Fields\DatePickerField;
use Botble\Base\Forms\FormAbstract;

class AnnouncementForm extends FormAbstract
{
    public function buildForm(): void
    {
        $this
            ->model(Announcement::class)
            ->setValidatorClass(AnnouncementRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('core/base::forms.name'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 255,
                ],
            ])
            ->add('content', 'editor', [
                'label' => trans('core/base::forms.content'),
                'required' => true,
                'attr' => [
                    'rows' => 2,
                    'without-buttons' => true,
                ],
            ])
            ->add('openRow1', 'html', [
                'html' => '<div class="row">',
            ])
            ->add(
                'start_date',
                DatePickerField::class,
                DatePickerFieldOption::make()
                    ->label(trans('plugins/announcement::announcements.start_date'))
                    ->wrapperAttributes(['class' => 'col-md-6 mb-3'])
                    ->withTimePicker()
            )
            ->add(
                'end_date',
                DatePickerField::class,
                DatePickerFieldOption::make()
                    ->label(trans('plugins/announcement::announcements.end_date'))
                    ->wrapperAttributes(['class' => 'col-md-6 mb-3'])
                    ->withTimePicker()
            )
            ->add('closeRow1', 'html', [
                'html' => '</div>',
            ])
            ->add('has_action', 'onOff', [
                'label' => trans('plugins/announcement::announcements.has_action'),
                'attr' => [
                    'data-bb-toggle' => 'collapse',
                    'data-bb-target' => '.has-action',
                ],
            ])
            ->add('openRow2', 'html', [
                'html' => sprintf('<div class="row has-action" style="%s">', $this->getModel()->has_action ? '' : 'display: none;'),
            ])
            ->add('action_label', 'text', [
                'label' => trans('plugins/announcement::announcements.action_label'),
                'wrapper' => [
                    'class' => 'col-md-6 mb-3',
                ],
            ])
            ->add('action_url', 'text', [
                'label' => trans('plugins/announcement::announcements.action_url'),
                'wrapper' => [
                    'class' => 'col-md-6 mb-3',
                ],
            ])
            ->add('action_open_new_tab', 'onOff', [
                'label' => trans('plugins/announcement::announcements.action_open_new_tab'),
                'wrapper' => [
                    'class' => 'col-md-12 mb-3',
                ],
            ])
            ->add('closeRow2', 'html', [
                'html' => '</div>',
            ])
            ->add('is_active', 'onOff', [
                'label' => trans('plugins/announcement::announcements.is_active'),
                'label_attr' => ['class' => 'control-label'],
                'default_value' => true,
            ])
            ->setBreakFieldPoint('is_active');
    }
}
