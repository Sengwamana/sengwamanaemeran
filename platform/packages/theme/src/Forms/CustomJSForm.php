<?php

namespace Botble\Theme\Forms;

use Botble\Base\Forms\FieldOptions\CodeEditorFieldOption;
use Botble\Base\Forms\Fields\CodeEditorField;
use Botble\Base\Forms\FormAbstract;
use Botble\Theme\Http\Requests\CustomJsRequest;

class CustomJSForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->setUrl(route('theme.custom-js.post'))
            ->setValidatorClass(CustomJsRequest::class)
            ->setActionButtons(view('core/base::forms.partials.form-actions', ['onlySave' => true])->render())
            ->add(
                'custom_header_js',
                CodeEditorField::class,
                CodeEditorFieldOption::make()
                    ->label(trans('packages/theme::theme.custom_header_js'))
                    ->helperText(trans('packages/theme::theme.custom_header_js_placeholder'))
                    ->value(setting('custom_header_js'))
                    ->mode('javascript')
                    ->maxLength(10000)
            )
            ->add(
                'custom_body_js',
                CodeEditorField::class,
                CodeEditorFieldOption::make()
                    ->label(trans('packages/theme::theme.custom_body_js'))
                    ->helperText(trans('packages/theme::theme.custom_body_js_placeholder'))
                    ->value(setting('custom_body_js'))
                    ->mode('javascript')
                    ->maxLength(10000)
            )
            ->add(
                'custom_footer_js',
                CodeEditorField::class,
                CodeEditorFieldOption::make()
                    ->label(trans('packages/theme::theme.custom_footer_js'))
                    ->helperText(trans('packages/theme::theme.custom_footer_js_placeholder'))
                    ->value(setting('custom_footer_js'))
                    ->mode('javascript')
                    ->maxLength(10000)
            );
    }
}
