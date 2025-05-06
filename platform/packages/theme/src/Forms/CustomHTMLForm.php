<?php

namespace Botble\Theme\Forms;

use Botble\Base\Forms\FieldOptions\CodeEditorFieldOption;
use Botble\Base\Forms\Fields\CodeEditorField;
use Botble\Base\Forms\FormAbstract;
use Botble\Theme\Http\Requests\CustomHtmlRequest;

class CustomHTMLForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->setUrl(route('theme.custom-html.post'))
            ->setValidatorClass(CustomHtmlRequest::class)
            ->setActionButtons(view('core/base::forms.partials.form-actions', ['onlySave' => true])->render())
            ->add(
                'custom_header_html',
                CodeEditorField::class,
                CodeEditorFieldOption::make()
                    ->label(trans('packages/theme::theme.custom_header_html'))
                    ->helperText(trans('packages/theme::theme.custom_header_html_placeholder'))
                    ->value(setting('custom_header_html'))
                    ->mode('html')
                    ->maxLength(2500)
            )
            ->add(
                'custom_body_html',
                CodeEditorField::class,
                CodeEditorFieldOption::make()
                    ->label(trans('packages/theme::theme.custom_body_html'))
                    ->helperText(trans('packages/theme::theme.custom_body_html_placeholder'))
                    ->value(setting('custom_body_html'))
                    ->mode('html')
                    ->maxLength(2500)
            )
            ->add(
                'custom_footer_html',
                CodeEditorField::class,
                CodeEditorFieldOption::make()
                    ->label(trans('packages/theme::theme.custom_footer_html'))
                    ->helperText(trans('packages/theme::theme.custom_footer_html_placeholder'))
                    ->value(setting('custom_footer_html'))
                    ->mode('html')
                    ->maxLength(2500)
            );
    }
}
