@if ($showStart)
    {!! Form::open(Arr::except($formOptions, ['template'])) !!}
@endif

<x-core-setting::section
    :title="$formOptions['section_title'] ?? ''"
    :description="$formOptions['section_description'] ?? ''"
>
    @if ($showFields)
        {{ $form->getOpenWrapperFormColumns() }}

        @foreach ($fields as $field)
            @continue(in_array($field->getName(), $exclude))

            {!! $field->render() !!}
        @endforeach

        {{ $form->getCloseWrapperFormColumns() }}
    @endif
</x-core-setting::section>

{!! $form->getActionButtons() !!}

@if ($showEnd)
    {!! Form::close() !!}
@endif

@pushif($form->getValidatorClass(), 'footer')
    {!! $form->renderValidatorJs() !!}
@endpushif
