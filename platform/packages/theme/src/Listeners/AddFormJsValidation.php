<?php

namespace Botble\Theme\Listeners;

use Botble\Base\Events\FormRendering;
use Botble\Base\Facades\AdminHelper;
use Botble\JsValidation\Facades\JsValidator;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Str;

class AddFormJsValidation
{
    public function handle(FormRendering $event): void
    {
        if (AdminHelper::isInAdmin()) {
            return;
        }

        $form = $event->form;

        if (! $form->getValidatorClass()) {
            return;
        }

        Theme::asset()
            ->container('footer')
            ->usePath(false)
            ->add('js-validation', 'vendor/core/core/js-validation/js/js-validation.js', ['jquery'], version: '1.0.1');

        $formSelector = $form->getDomSelector();

        Theme::asset()
            ->container('footer')
            ->writeContent(
                'js-validation-form-rules-' . Str::slug($formSelector),
                JsValidator::formRequest($form->getValidatorClass(), $formSelector)->render(),
                ['jquery']
            );
    }
}
