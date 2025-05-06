<?php

namespace Botble\Theme;

use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\Fields\CheckboxField;
use Botble\Base\Forms\Fields\DateField;
use Botble\Base\Forms\Fields\DatetimeField;
use Botble\Base\Forms\Fields\EmailField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\PasswordField;
use Botble\Base\Forms\Fields\PhoneNumberField;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Illuminate\Support\Str;

abstract class FormFront extends FormAbstract
{
    protected ?string $formEndKey = null;

    protected ?string $formInputWrapperClass = null;

    protected ?string $formInputClass = null;

    protected ?string $formLabelClass = null;

    protected bool $addAsteriskToMandatoryFields = false;

    public static function formTitle(): string
    {
        return Str::title(Str::snake(class_basename(static::class), ' '));
    }

    public function buildForm(): void
    {
        $this->add(
            'form_front_form_start',
            HtmlField::class,
            HtmlFieldOption::make()
                ->content(apply_filters('form_front_form_start', '', $this))
        );

        parent::buildForm();

        $this->add(
            'form_front_form_end',
            HtmlField::class,
            HtmlFieldOption::make()
                ->content(apply_filters('form_front_form_end', '', $this))
        );

        $this->addBefore(
            'submit',
            'form_front_before_submit_button',
            HtmlField::class,
            HtmlFieldOption::make()
                ->content(apply_filters('form_front_before_submit_button', '', $this))
        );
    }

    public function setFormEndKey(string $key): static
    {
        $this->formEndKey = $key;

        return $this;
    }

    public function getFormEndKey(): ?string
    {
        return $this->formEndKey;
    }

    public function setFormInputClass(string $class): static
    {
        $this->formInputClass = $class;

        return $this;
    }

    public function getFormInputClass(): ?string
    {
        return $this->formInputClass;
    }

    public function getFormLabelClass(): ?string
    {
        return $this->formLabelClass;
    }

    public function getFormInputWrapperClass(): ?string
    {
        return $this->formInputWrapperClass;
    }

    public function setFormInputWrapperClass(string $class): static
    {
        $this->formInputWrapperClass = $class;

        return $this;
    }

    public function setFormLabelClass(string $class): static
    {
        $this->formLabelClass = $class;

        return $this;
    }

    public function addAsteriskToMandatoryFields(bool $addAsteriskToMandatoryFields = true): static
    {
        $this->addAsteriskToMandatoryFields = $addAsteriskToMandatoryFields;

        return $this;
    }

    public function renderForm(array $options = [], bool $showStart = true, bool $showFields = true, bool $showEnd = true): string
    {
        foreach ($this->getFields() as &$field) {
            if (! in_array($field->getType(), [
                TextField::class,
                TextareaField::class,
                EmailField::class,
                PasswordField::class,
                PhoneNumberField::class,
                NumberField::class,
                SelectField::class,
                RadioField::class,
                OnOffCheckboxField::class,
                CheckboxField::class,
                DateField::class,
                DatetimeField::class,
                'text',
                'email',
                'password',
                'number',
                'checkbox',
                'radio',
                'select',
                'textarea',
            ])) {
                continue;
            }

            if ($this->getFormInputWrapperClass()) {
                $field->setOption('wrapper.class', $this->getFormInputWrapperClass());
            }

            if ($this->getFormInputClass()) {
                $field->setOption('attr.class', $this->getFormInputClass());

                if (in_array($field->getType(), [CheckboxField::class, OnOffCheckboxField::class])) {
                    $field->setOption('attr.class', trim(str_replace('form-control', '', $this->getFormInputClass())));
                }
            }

            if ($this->getFormLabelClass()) {
                $labelClass = $this->getFormLabelClass();

                if (in_array($field->getType(), [CheckboxField::class, OnOffCheckboxField::class, 'checkbox', 'radio'])) {
                    $labelClass = str_replace('sr-only', '', $labelClass);
                    $labelClass = str_replace('d-none', '', $labelClass);
                }

                $field->setOption('label_attr.class', $labelClass . str_replace('form-label', '', $field->getOption('label_attr.class', '')));
            }
        }

        if ($this->addAsteriskToMandatoryFields) {
            foreach ($this->getFields() as &$field) {
                if ($field->getOption('required') && $field->getOption('attr.placeholder')) {
                    $field->setOption('attr.placeholder', $field->getOption('attr.placeholder') . ' *');
                }
            }
        }

        return parent::renderForm($options, $showStart, $showFields, $showEnd);
    }
}
