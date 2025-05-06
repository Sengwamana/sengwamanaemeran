@extends($layout ?? BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @if ($showStart)
        {!! Form::open(Arr::except($formOptions, ['template'])) !!}
    @endif

    @php
        do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), $form->getModel());
    @endphp

    <div class="row">
        <div class="gap-3 col-md-9">
            @if ($showFields && $form->hasMainFields())
                <x-core::card class="mb-3">
                    <x-core::card.body>
                        <div class="{{ $form->getWrapperClass() }}">
                            {{ $form->getOpenWrapperFormColumns() }}

                            @foreach ($fields as $key => $field)
                                @if ($field->getName() == $form->getBreakFieldPoint())
                                @break

                            @else
                                @unset($fields[$key])
                            @endif

                            @continue(in_array($field->getName(), $exclude))

                            {!! $field->render() !!}
                            @if (defined('BASE_FILTER_SLUG_AREA') && $field->getName() == SlugHelper::getColumnNameToGenerateSlug($form->getModel()))
                                {!! apply_filters(BASE_FILTER_SLUG_AREA, null, $form->getModel()) !!}
                            @endif
                        @endforeach

                        {{ $form->getCloseWrapperFormColumns() }}
                    </div>
                </x-core::card.body>
            </x-core::card>
        @endif

        @foreach ($form->getMetaBoxes() as $key => $metaBox)
            {!! $form->getMetaBox($key) !!}
        @endforeach

        @php
            do_action(BASE_ACTION_META_BOXES, 'advanced', $form->getModel());
        @endphp
    </div>

    <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
        {!! $form->getActionButtons() !!}

        @php
            do_action(BASE_ACTION_META_BOXES, 'top', $form->getModel());
        @endphp

        @foreach ($fields as $field)
            @if (!in_array($field->getName(), $exclude))
                @if (in_array($field->getType(), ['hidden', \Botble\Base\Forms\Fields\HiddenField::class]))
                    {!! $field->render() !!}
                @else
                    <x-core::card class="meta-boxes">
                        <x-core::card.header>
                            <x-core::card.title>
                                {!! Form::customLabel($field->getName(), $field->getOption('label'), $field->getOption('label_attr')) !!}
                            </x-core::card.title>
                        </x-core::card.header>

                        @php
                            $bodyAttrs = Arr::get($field->getOptions(), 'card-body-attrs', []);

                            if (! Arr::has($bodyAttrs, 'class')) {
                                $bodyAttrs['class'] = '';
                            }

                            $bodyAttrs['class'] .= ' card-body';
                        @endphp

                        <div {!! Html::attributes($bodyAttrs) !!}>
                            {!! $field->render([], in_array($field->getType(), ['radio', 'checkbox'])) !!}
                        </div>
                    </x-core::card>
                @endif
            @endif
        @endforeach

        @php
            do_action(BASE_ACTION_META_BOXES, 'side', $form->getModel());
        @endphp
    </div>
</div>

@if ($showEnd)
    {!! Form::close() !!}
@endif

@yield('form_end')
@endsection

@if ($form->getValidatorClass())
@if ($form->isUseInlineJs())
    {!! Assets::scriptToHtml('jquery') !!}
    {!! Assets::scriptToHtml('form-validation') !!}
    {!! $form->renderValidatorJs() !!}
@else
    @push('footer')
        {!! $form->renderValidatorJs() !!}
    @endpush
@endif
@endif
