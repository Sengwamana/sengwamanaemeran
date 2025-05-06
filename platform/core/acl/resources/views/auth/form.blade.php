@extends('core/acl::layouts.guest')

@section('content')
    @if ($showStart)
        {!! Form::open(Arr::except($formOptions, ['template'])) !!}
    @endif

    @php
        do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), $form->getModel());
    @endphp

    @if ($showFields && $form->hasMainFields())
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
        @endforeach

        {{ $form->getCloseWrapperFormColumns() }}
    </div>
@endif

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
