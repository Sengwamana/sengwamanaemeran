<?php

namespace Botble\Base\Providers;

use Botble\Base\Forms\Form as PlainFormClass;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Forms\FormHelper;
use Botble\Base\Supports\ServiceProvider;
use Illuminate\Config\Repository;
use Kris\LaravelFormBuilder\Events\FormComponentRegistering;

class FormServiceProvider extends ServiceProvider
{
    protected bool $defer = true;

    public function register(): void
    {
        /**
         * @var Repository $config
         */
        $config = $this->app['config'];

        $config->set([
            'laravel-form-builder' => [
                ...$config->get('laravel-form-builder', []),
                'defaults.wrapper_class' => 'mb-3 position-relative',
                'defaults.label_class' => 'form-label',
                'defaults.field_error_class' => 'is-invalid',
                'defaults.help_block_class' => 'form-hint',
                'defaults.error_class' => 'invalid-feedback',
                'defaults.help_block_tag' => 'small',
                'defaults.select' => [
                    'field_class' => 'form-select',
                ],
                'plain_form_class' => PlainFormClass::class,
                'form_builder_class' => FormBuilder::class,
                'form_helper_class' => FormHelper::class,
            ],
        ]);

        $this->app['events']->listen(FormComponentRegistering::class, function (FormComponentRegistering $event): void {
            $form = $event->form;
            $form->component('mediaImage', 'core/base::forms.partials.image', [
                'name',
                'value' => null,
                'attributes' => [],
            ]);

            $form->component('mediaImages', 'core/base::forms.partials.images', [
                'name',
                'values' => [],
                'attributes' => [],
            ]);

            $form->component('mediaFile', 'core/base::forms.partials.file', [
                'name',
                'value' => null,
                'attributes' => [],
            ]);

            $form->component('modalAction', 'core/base::forms.partials.modal', [
                'name',
                'title',
                'type' => null,
                'content' => null,
                'action_id' => null,
                'action_name' => null,
                'modal_size' => null,
            ]);

            $form->component('helper', 'core/base::forms.partials.helper', ['content', 'icon']);

            $form->component('onOff', 'core/base::forms.partials.on-off', [
                'name',
                'value' => false,
                'attributes' => [],
            ]);

            $form->component('onOffCheckbox', 'core/base::forms.partials.on-off-checkbox', [
                'name',
                'value' => false,
                'attributes' => [],
            ]);

            /**
             * Custom checkbox
             * Every checkbox will not have the same name
             */
            $form->component('customCheckbox', 'core/base::forms.partials.custom-checkbox', [
                /**
                 * @var array $values
                 * @template: [
                 *      [string $name, string $value, string $label, bool $selected, bool $disabled],
                 *      [string $name, string $value, string $label, bool $selected, bool $disabled],
                 *      [string $name, string $value, string $label, bool $selected, bool $disabled],
                 * ]
                 */
                'values',
                'inline',
            ]);

            /**
             * Custom radio
             * Every radio in list must have the same name
             */
            $form->component('customRadio', 'core/base::forms.partials.custom-radio', [
                /**
                 * @var string $name
                 */
                'name',
                /**
                 * @var array $values
                 * @template: [
                 *      [string $value, string $label, bool $disabled],
                 *      [string $value, string $label, bool $disabled],
                 *      [string $value, string $label, bool $disabled],
                 * ]
                 */
                'values',
                /**
                 * @var string|null $selected
                 */
                'selected' => null,
                'attributes' => [],
            ]);

            $form->component('error', 'core/base::forms.partials.error', [
                'name',
                'errors' => null,
            ]);

            $form->component('editor', 'core/base::forms.partials.editor', [
                'name',
                'value' => null,
                'attributes' => [],
            ]);

            $form->component('ckeditor', 'core/base::forms.partials.ckeditor', [
                'name',
                'value' => null,
                'attributes' => [],
            ]);

            $form->component('tinymce', 'core/base::forms.partials.tinymce', [
                'name',
                'value' => null,
                'attributes' => [],
            ]);

            $form->component('customSelect', 'core/base::forms.partials.custom-select', [
                'name',
                'choices' => [],
                'selected' => null,
                'selectAttributes' => [],
                'optionsAttributes' => [],
                'optgroupsAttributes' => [],
            ]);

            $form->component('autocomplete', 'core/base::forms.partials.autocomplete', [
                'name',
                'choices' => [],
                'selected' => null,
                'selectAttributes' => [],
                'optionsAttributes' => [],
                'optgroupsAttributes' => [],
            ]);

            $form->component('googleFonts', 'core/base::forms.partials.google-fonts', [
                'name',
                'selected' => null,
                'selectAttributes' => [],
                'optionsAttributes' => [],
            ]);

            $form->component('customColor', 'core/base::forms.partials.color', [
                'name',
                'value' => null,
                'attributes' => [],
            ]);

            $form->component('repeater', 'core/base::forms.partials.repeater', [
                'name',
                'value' => null,
                'fields' => [],
                'attributes' => [],
            ]);

            $form->component('phoneNumber', 'core/base::forms.partials.phone-number', [
                'name',
                'value' => null,
                'attributes' => [],
            ]);

            $form->component('datePicker', 'core/base::forms.partials.date-picker', [
                'name',
                'value' => null,
                'attributes' => [],
            ]);

            $form->component('timePicker', 'core/base::forms.partials.time-picker', [
                'name',
                'value' => null,
                'attributes' => [],
            ]);

            $form->component('codeEditor', 'core/base::forms.partials.code-editor', [
                'name',
                'value' => null,
                'attributes' => [],
            ]);

            $form->component('nestedSelectDropdown', 'core/base::forms.partials.nested-select-dropdown', [
                'name',
                'choices' => [],
                'selected' => null,
                'attributes' => [],
                'selectAttributes' => [],
                'optionsAttributes' => [],
                'optgroupsAttributes' => [],
            ]);

            $form->component('uiSelector', 'core/base::forms.partials.ui-selector', [
                'name',
                'value' => null,
                'choices' => [],
                'attributes' => [],
            ]);

            $form->component('multiChecklist', 'core/base::forms.partials.multi-checklist', [
                'name',
                'value' => null,
                'choices' => [],
                'attributes' => [],
                'emptyValue' => null,
                'inline' => false,
                'asDropdown' => false,
                'ajaxUrl' => null,
            ]);

            $form->component('coreIcon', 'core/base::forms.partials.core-icon', [
                'name',
                'value' => null,
                'attributes' => [],
            ]);

            $form->component('customLabel', 'core/base::forms.partials.label', [
                'name',
                'label',
                'attributes' => [],
                'escapeHtml' => true,
            ]);
        });
    }
}
