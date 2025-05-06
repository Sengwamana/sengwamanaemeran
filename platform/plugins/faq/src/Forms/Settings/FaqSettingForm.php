<?php

namespace Botble\Faq\Forms\Settings;

use Botble\Faq\Http\Requests\Settings\FaqSettingRequest;
use Botble\Setting\Forms\SettingForm;

class FaqSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/faq::faq.settings.title'))
            ->setSectionDescription(trans('plugins/faq::faq.settings.description'))
            ->setValidatorClass(FaqSettingRequest::class)
            ->add('enable_faq_schema', 'onOffCheckbox', [
                'label' => trans('plugins/faq::faq.settings.enable_faq_schema'),
                'value' => setting('enable_faq_schema', false),
                'wrapper' => [
                    'class' => 'mb-0',
                ],
                'help_block' => [
                    'text' => trans('plugins/faq::faq.settings.enable_faq_schema_description', [
                        'url' => sprintf('<a href="%s">%s</a>', $url = 'https://developers.google.com/search/docs/data-types/faqpage', $url),
                    ]),
                ],
            ]);
    }
}
