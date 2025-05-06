<?php

namespace Botble\Theme\Forms;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\CodeEditorFieldOption;
use Botble\Base\Forms\Fields\CodeEditorField;
use Botble\Base\Forms\FormAbstract;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Http\Requests\CustomCssRequest;
use Illuminate\Support\Facades\File;

class CustomCSSForm extends FormAbstract
{
    public function setup(): void
    {
        $css = null;
        $file = Theme::getStyleIntegrationPath();

        if (File::exists($file)) {
            $css = BaseHelper::getFileData($file, false);
        }

        $this
            ->setUrl(route('theme.custom-css.post'))
            ->setValidatorClass(CustomCssRequest::class)
            ->setActionButtons(view('core/base::forms.partials.form-actions', ['onlySave' => true])->render())
            ->add(
                'custom_css',
                CodeEditorField::class,
                CodeEditorFieldOption::make()
                    ->label(trans('packages/theme::theme.custom_css'))
                    ->value($css)
                    ->mode('css')
                    ->maxLength(100000)
            );
    }
}
