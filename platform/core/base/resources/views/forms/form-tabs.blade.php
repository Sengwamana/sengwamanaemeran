@extends($layout ?? BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @if ($showStart)
        {!! Form::open(Arr::except($formOptions, ['template'])) !!}
    @endif

    @php
        do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), $form->getModel());
        $columns = $form->getFormOption('columns');
    @endphp

    <div class="row">
        <div class="col-md-9">
            <x-core::card class="mb-3">
                <x-core::card.header>
                    <x-core::tab class="card-header-tabs">
                        <x-core::tab.item
                            id="tabs-detail"
                            :is-active="true"
                            :label="trans('core/base::tabs.detail')"
                        />

                        {!! apply_filters(BASE_FILTER_REGISTER_CONTENT_TABS, null, $form->getModel()) !!}
                    </x-core::tab>
                </x-core::card.header>

                <x-core::card.body>
                    <x-core::tab.content>
                        <x-core::tab.pane id="tabs-detail" :is-active="true">
                            @if ($showFields)
                                {{ $form->getOpenWrapperFormColumns() }}

                                @foreach ($fields as $key => $field)
                                    @break($field->getName() === $form->getBreakFieldPoint())
                                    @unset($fields[$key])
                                    @continue(in_array($field->getName(), $exclude))

                                    {!! $field->render() !!}

                                    @if (defined('BASE_FILTER_SLUG_AREA') &&
                                            $field->getName() === SlugHelper::getColumnNameToGenerateSlug($form->getModel()))
                                        {!! apply_filters(BASE_FILTER_SLUG_AREA, null, $form->getModel()) !!}
                                    @endif
                                @endforeach

                                {{ $form->getCloseWrapperFormColumns() }}
                            @endif
                        </x-core::tab.pane>
                        {!! apply_filters(BASE_FILTER_REGISTER_CONTENT_TAB_INSIDE, null, $form->getModel()) !!}
                    </x-core::tab.content>
                </x-core::card.body>
            </x-core::card>

            @foreach ($form->getMetaBoxes() as $key => $metaBox)
                {!! $form->getMetaBox($key) !!}
            @endforeach

            @php
                do_action(BASE_ACTION_META_BOXES, 'advanced', $form->getModel());
            @endphp

            @yield('form_main_end')
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

                            <x-core::card.body>
                                {!! $field->render([], false) !!}
                            </x-core::card.body>
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
