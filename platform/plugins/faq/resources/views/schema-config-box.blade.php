<div class="faq-schema-items">
    {!! Form::repeater('faq_schema_config', $value, [
        [
            'type' => 'textarea',
            'label' => trans('plugins/faq::faq.question'),
            'required' => true,
            'attributes' => [
                'name' => 'question',
                'value' => null,
                'options' => [
                    'class' => 'form-control',
                    'data-counter' => 1000,
                    'rows' => 1,
                ],
            ],
        ],
        [
            'type' => 'textarea',
            'label' => trans('plugins/faq::faq.answer'),
            'required' => true,
            'attributes' => [
                'name' => 'answer',
                'value' => null,
                'options' => [
                    'class' => 'form-control',
                    'data-counter' => 1000,
                    'rows' => 1,
                ],
            ],
        ],
    ]) !!}
</div>

<div class="d-inline">
    <span>{{ trans('plugins/faq::faq.or') }}</span>
    <a href="javascript:void(0)" data-bb-toggle="select-from-existing">
        {{ trans('plugins/faq::faq.select_from_existing') }}
    </a>
</div>

<div class="existing-faq-schema-items mt-2" @style(['display: none' => empty($selectedFaqs) || ! $faqs])>
    @if($faqs)
        {{ Form::multiChecklist('selected_existing_faqs[]', $selectedFaqs, $faqs, [], false, false, true) }}
    @else
        <p class="text-muted mb-0">
            {!! BaseHelper::clean(trans(
                'plugins/faq::faq.no_existing',
                ['link' => Html::link(route('faq.create'), trans('plugins/faq::faq.faqs_menu_name'), ['target' => '_blank'])])
            ) !!}
        </p>
    @endif
</div>
