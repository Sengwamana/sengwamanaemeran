<?php

namespace Botble\Portfolio\Forms\Fronts;

use Botble\Base\Forms\FieldOptions\ButtonFieldOption;
use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
use Botble\Base\Forms\FieldOptions\EmailFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\InputFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\EmailField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Portfolio\Enums\CustomFieldType;
use Botble\Portfolio\Http\Requests\QuoteRequest;
use Botble\Portfolio\Models\CustomField;
use Botble\Portfolio\Models\Quote;
use Botble\Theme\FormFront;
use Closure;
use Illuminate\Database\Eloquent\Collection;

class QuotationForm extends FormFront
{
    protected string $errorBag = 'quotation';

    protected ?string $formInputWrapperClass = 'quotation-form-group';

    protected ?string $formInputClass = 'quotation-form-input';

    public static function formTitle(): string
    {
        return __('QuotationForm');
    }

    public function setup(): void
    {
        $customFields = CustomField::query()
            ->wherePublished()->with('options')
            ->orderBy('order')
            ->get();

        $this
            ->contentOnly()
            ->model(Quote::class)
            ->setUrl(route('portfolio.request-quote'))
            ->setValidatorClass(QuoteRequest::class)
            ->setFormOption('class', 'quotation-form')
            ->add(
                'filters_before_form',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(apply_filters('pre_quotation_form', null))
                    ->toArray()
            )
            ->add(
                'name',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Your name'))
                    ->toArray()
            )
            ->add(
                'email',
                EmailField::class,
                EmailFieldOption::make()
                    ->label(__('Your email'))
                    ->toArray()
            )
            ->when($customFields, function (QuotationForm $form, Collection $customFields): void {
                foreach ($customFields as $customField) {
                    $options = $customField->options()
                        ->orderBy('order')
                        ->pluck('label', 'id')->all();

                    $fieldOptions = match ($customField->type->getValue()) {
                        CustomFieldType::NUMBER => NumberFieldOption::make()
                            ->when($customField->placeholder, function (InputFieldOption $options, string $placeholder): void {
                                $options->placeholder($placeholder);
                            }),
                        CustomFieldType::DROPDOWN => SelectFieldOption::make()
                            ->when($customField->placeholder, function (SelectFieldOption $fieldOptions, string $placeholder) use ($options): void {
                                $fieldOptions->choices(['' => $placeholder, ...$options]);
                            }, function (SelectFieldOption $fieldOptions) use ($options): void {
                                $fieldOptions->choices($options);
                            }),
                        CustomFieldType::CHECKBOX => CheckboxFieldOption::make(),
                        default => TextFieldOption::make()
                            ->wrapperAttributes(['class' => $this->formInputWrapperClass])
                            ->cssClass($this->formInputClass)
                            ->when($customField->placeholder, function (InputFieldOption $options, string $placeholder): void {
                                $options->placeholder($placeholder);
                            }),
                    };

                    $field = match ($customField->type->getValue()) {
                        CustomFieldType::NUMBER => NumberField::class,
                        CustomFieldType::TEXTAREA => TextareaField::class,
                        CustomFieldType::DROPDOWN => SelectField::class,
                        CustomFieldType::CHECKBOX => OnOffCheckboxField::class,
                        default => TextField::class,
                    };

                    $this->addColumnWrapper("custom_field_{$customField->id}_wrapper", function (self $form) use ($customField, $field, $fieldOptions): void {
                        $form->add(
                            "custom_fields[$customField->id]",
                            $field,
                            $fieldOptions
                                ->label($customField->name)
                                ->required($customField->required)
                        );
                    }, 12);
                }
            })
            ->add(
                'message',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('Message'))
                    ->toArray()
            )
            ->add(
                'filters_after_form',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(apply_filters('after_quotation_form', null))
                    ->toArray()
            )
            ->addWrappedField(
                'submit',
                'submit',
                ButtonFieldOption::make()
                    ->cssClass('quotation-button')
                    ->label(__('Send'))
                    ->toArray()
            );
    }

    protected function addWrappedField(string $name, string $type, array $options): static
    {
        $this->add(
            "open_{$name}_field_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()->content('<div class="quotation-form-group">')->toArray()
        );

        $this->add($name, $type, $options);

        return $this->add(
            "close_{$name}_field_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()->content('</div>')->toArray()
        );
    }

    protected function addRowWrapper(string $name, Closure $callback): static
    {
        $this->add(
            "open_{$name}_row_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()->content('<div class="quotation-form-row row">')->toArray()
        );

        $callback($this);

        return $this->add(
            "close_{$name}_row_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()->content('</div>')->toArray()
        );
    }

    protected function addColumnWrapper(string $name, Closure $callback, int $column = 6): static
    {
        $this->add(
            "open_{$name}_column_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()->content(sprintf('<div class="quotation-column-%s col-md-%s quotation-field-%s">', $column, $column, $name))->toArray()
        );

        $callback($this);

        return $this->add(
            "close_{$name}_column_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()->content('</div>')->toArray()
        );
    }
}
