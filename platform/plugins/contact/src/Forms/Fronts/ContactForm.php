<?php

namespace Botble\Contact\Forms\Fronts;

use Botble\Base\Facades\Html;
use Botble\Base\Forms\FieldOptions\ButtonFieldOption;
use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\InputFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\DateField;
use Botble\Base\Forms\Fields\DatetimeField;
use Botble\Base\Forms\Fields\EmailField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\TimeField;
use Botble\Contact\Enums\CustomFieldType;
use Botble\Contact\Http\Requests\ContactRequest;
use Botble\Contact\Models\Contact;
use Botble\Contact\Models\CustomField;
use Botble\Theme\Facades\Theme;
use Botble\Theme\FormFront;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Throwable;

class ContactForm extends FormFront
{
    protected string $errorBag = 'contact';

    protected ?string $formInputWrapperClass = 'contact-form-group';

    protected ?string $formInputClass = 'contact-form-input';

    public static function formTitle(): string
    {
        return trans('plugins/contact::contact.contact_form');
    }

    public function setup(): void
    {
        $data = $this->getModel();

        Theme::asset()
            ->usePath(false)
            ->add('contact-css', asset('vendor/core/plugins/contact/css/contact-public.css'), [], [], '1.0.1');

        Theme::asset()
            ->container('footer')
            ->usePath(false)
            ->add(
                'contact-public-js',
                asset('vendor/core/plugins/contact/js/contact-public.js'),
                ['jquery'],
                [],
                '1.0.1'
            );

        try {
            $displayFields = array_filter(explode(',', (string) Arr::get($data, 'display_fields'))) ?: ['phone', 'email', 'address', 'subject'];
        } catch (Throwable) {
            $displayFields = ['phone', 'email', 'address', 'subject'];
        }

        try {
            $mandatoryFields = array_filter(explode(',', (string) Arr::get($data, 'mandatory_fields'))) ?: ['email'];
        } catch (Throwable) {
            $mandatoryFields = ['email'];
        }

        $this
            ->contentOnly()
            ->model(Contact::class)
            ->setUrl(route('public.send.contact'))
            ->setValidatorClass(ContactRequest::class)
            ->setFormOption('class', 'contact-form')
            ->add(
                'filters_before_form',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(apply_filters('pre_contact_form', null))
            )
            ->add(
                'required_fields',
                'hidden',
                TextFieldOption::make()
                    ->value(Arr::get($data, 'mandatory_fields'))
            )
            ->add(
                'display_fields',
                'hidden',
                TextFieldOption::make()
                    ->value(Arr::get($data, 'display_fields'))
            )
            ->addRowWrapper('form_wrapper', function (self $form) use ($displayFields, $mandatoryFields): void {
                $customFields = CustomField::query()
                    ->wherePublished()
                    ->orderBy('order')
                    ->get();

                $form
                    ->addColumnWrapper('name_wrapper', function (self $form): void {
                        $form
                            ->add(
                                'name',
                                TextField::class,
                                TextFieldOption::make()
                                    ->required()
                                    ->label(__('Name'))
                                    ->placeholder(__('Your Name'))
                                    ->wrapperAttributes(['class' => $this->formInputWrapperClass])
                                    ->cssClass($this->formInputClass)
                                    ->maxLength(-1)
                            );
                    })
                    ->when(in_array('email', $displayFields), function (self $form) use ($mandatoryFields): void {
                        $form
                            ->addColumnWrapper('email_wrapper', function (self $form) use ($mandatoryFields): void {
                                $form
                                    ->add(
                                        'email',
                                        EmailField::class,
                                        TextFieldOption::make()
                                            ->when(in_array('email', $mandatoryFields), function (TextFieldOption $option): void {
                                                $option->required();
                                            })
                                            ->label(__('Email'))
                                            ->placeholder(__('Your Email'))
                                            ->wrapperAttributes(['class' => $this->formInputWrapperClass])
                                            ->cssClass($this->formInputClass)
                                            ->maxLength(-1)
                                    );
                            });
                    })
                    ->when(in_array('address', $displayFields), function (self $form) use ($mandatoryFields): void {
                        $form->addColumnWrapper('address_wrapper', function (self $form) use ($mandatoryFields): void {
                            $form
                                ->add(
                                    'address',
                                    TextField::class,
                                    TextFieldOption::make()
                                        ->when(in_array('address', $mandatoryFields), function (TextFieldOption $option): void {
                                            $option->required();
                                        })
                                        ->label(__('Address'))
                                        ->placeholder(__('Your Address'))
                                        ->wrapperAttributes(['class' => $this->formInputWrapperClass])
                                        ->cssClass($this->formInputClass)
                                        ->maxLength(-1)
                                );
                        });
                    })
                    ->when(in_array('phone', $displayFields), function (self $form) use ($mandatoryFields): void {
                        $form->addColumnWrapper('phone_wrapper', function (self $form) use ($mandatoryFields): void {
                            $form
                                ->add(
                                    'phone',
                                    TextField::class,
                                    TextFieldOption::make()
                                        ->when(in_array('phone', $mandatoryFields), function (TextFieldOption $option): void {
                                            $option->required();
                                        })
                                        ->label(__('Phone'))
                                        ->placeholder(__('Your Phone'))
                                        ->wrapperAttributes(['class' => $this->formInputWrapperClass])
                                        ->cssClass($this->formInputClass)
                                        ->maxLength(-1)
                                );
                        });
                    })
                    ->when(in_array('subject', $displayFields), function (self $form) use ($mandatoryFields): void {
                        $form->addColumnWrapper('subject_wrapper', function (self $form) use ($mandatoryFields): void {
                            $form->add(
                                'subject',
                                TextField::class,
                                TextFieldOption::make()
                                    ->when(in_array('subject', $mandatoryFields), function (TextFieldOption $option): void {
                                        $option->required();
                                    })
                                    ->label(__('Subject'))
                                    ->placeholder(__('Subject'))
                                    ->wrapperAttributes(['class' => $this->formInputWrapperClass])
                                    ->cssClass($this->formInputClass)
                                    ->maxLength(-1)
                            );
                        }, 12);
                    })
                    ->when($customFields, function (ContactForm $form, Collection $customFields): void {
                        foreach ($customFields as $customField) {
                            $options = $customField->options()->select('id', 'label', 'value')->get()->mapWithKeys(function ($option) {
                                return [$option->value => $option->label];
                            })->all();

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
                                CustomFieldType::RADIO => RadioFieldOption::make()->choices($options),
                                default => TextFieldOption::make()
                                    ->wrapperAttributes(['class' => $this->formInputWrapperClass])
                                    ->cssClass($this->formInputClass)
                                    ->placeholder($customField->placeholder ?: $customField->name)
                            };

                            $field = match ($customField->type->getValue()) {
                                CustomFieldType::NUMBER => NumberField::class,
                                CustomFieldType::TEXTAREA => TextareaField::class,
                                CustomFieldType::DROPDOWN => SelectField::class,
                                CustomFieldType::CHECKBOX => OnOffCheckboxField::class,
                                CustomFieldType::RADIO => RadioField::class,
                                CustomFieldType::DATE => DateField::class,
                                CustomFieldType::DATETIME => DatetimeField::class,
                                CustomFieldType::TIME => TimeField::class,
                                default => TextField::class,
                            };

                            $form->addColumnWrapper("custom_field_{$customField->id}_wrapper", function (self $form) use ($customField, $field, $fieldOptions): void {
                                $form->add(
                                    "contact_custom_fields[$customField->id]",
                                    $field,
                                    $fieldOptions
                                        ->label($customField->name)
                                        ->required($customField->required)
                                        ->wrapperAttributes(['class' => $this->formInputWrapperClass])
                                );
                            }, 12);
                        }
                    });
            })
            ->addRowWrapper(
                'content',
                function (self $form): void {
                    $form->addColumnWrapper(
                        'content',
                        function (self $form): void {
                            $form->add(
                                'content',
                                TextareaField::class,
                                TextareaFieldOption::make()
                                    ->required()
                                    ->label(__('Content'))
                                    ->placeholder(__('Write your message here'))
                                    ->wrapperAttributes(['class' => $this->formInputWrapperClass])
                                    ->cssClass($this->formInputClass)
                                    ->rows(5)
                                    ->maxLength(-1)
                            );
                        },
                        12
                    );
                }
            )
            ->add(
                'filters_after_form',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(apply_filters('after_contact_form', null))
            )
            ->add(
                'agree_terms_and_policy',
                OnOffCheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(
                        ($privacyPolicyUrl = Theme::termAndPrivacyPolicyUrl())
                            ? __('I agree to the :link', ['link' => Html::link($privacyPolicyUrl, __('Terms and Privacy Policy'), attributes: ['class' => 'text-decoration-underline', 'target' => '_blank'])])
                            : __('I agree to the Terms and Privacy Policy')
                    )
            )
            ->addWrappedField(
                'submit',
                'submit',
                ButtonFieldOption::make()
                    ->cssClass('contact-button')
                    ->label(__('Send'))
            )
            ->addWrappedField(
                'messages',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(<<<'HTML'
                        <div class="contact-message contact-success-message" style="display: none"></div>
                        <div class="contact-message contact-error-message" style="display: none"></div>
                    HTML)
            );
    }

    protected function addWrappedField(string $name, string $type, array|Arrayable $options): static
    {
        $this->add(
            "open_{$name}_field_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()
                ->content('<div class="contact-form-group">')
        );

        $this->add($name, $type, $options);

        return $this->add(
            "close_{$name}_field_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()->content('</div>')
        );
    }

    protected function addRowWrapper(string $name, Closure $callback): static
    {
        $this->add(
            "open_{$name}_row_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()->content('<div class="contact-form-row row">')
        );

        $callback($this);

        return $this->add(
            "close_{$name}_row_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()->content('</div>')
        );
    }

    protected function addColumnWrapper(string $name, Closure $callback, int $column = 6): static
    {
        $this->add(
            "open_{$name}_column_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()->content(sprintf('<div class="contact-column-%s col-md-%s contact-field-%s">', $column, $column, $name))
        );

        $callback($this);

        return $this->add(
            "close_{$name}_column_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()->content('</div>')
        );
    }
}
