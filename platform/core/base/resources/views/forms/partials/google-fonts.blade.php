@php
    $field['options'] = BaseHelper::getFonts();
@endphp

{!! Form::customSelect(
    $name,
    ['' => __('-- Select --')] + array_combine($field['options'], $field['options']),
    $selected,
    ['data-bb-toggle' => 'google-font-selector'],
) !!}

@once
    @push('footer')
        @foreach(array_chunk($field['options'], 200) as $fonts)
            {!! Html::style(
                BaseHelper::getGoogleFontsURL() .
                    '/css?family=' .
                    implode('|', array_map('urlencode', array_filter($fonts))) .
                    '&display=swap',
            ) !!}
        @endforeach
    @endpush
@endonce
