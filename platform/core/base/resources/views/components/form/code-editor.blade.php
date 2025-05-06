@php use Botble\Base\Facades\Assets; @endphp
@props([
    'id' => null,
    'label' => null,
    'name' => null,
    'value' => old($name),
    'helperText' => null,
    'errorKey' => $name,
    'mode' => null,
])

@php
    $id = $id ?: $name . '_' . md5($name);
    $mode = $mode === 'html' ? 'htmlmixed' : $mode;

    $css = [
        'vendor/core/core/base/libraries/codemirror/lib/codemirror.css',
        'vendor/core/core/base/libraries/codemirror/addon/hint/show-hint.css',
    ];

    $js = [
        'vendor/core/core/base/libraries/codemirror/lib/codemirror.js',
        'vendor/core/core/base/libraries/codemirror/addon/hint/show-hint.js',
        'vendor/core/core/base/libraries/codemirror/addon/hint/anyword-hint.js',
        'vendor/core/core/base/libraries/codemirror/addon/display/autorefresh.js',
    ];

    switch ($mode) {
        case 'htmlmixed':
            $js = [
                ...$js,
                'vendor/core/core/base/libraries/codemirror/mode/htmlmixed.js',
                'vendor/core/core/base/libraries/codemirror/mode/css.js',
                'vendor/core/core/base/libraries/codemirror/mode/javascript.js',
                'vendor/core/core/base/libraries/codemirror/mode/xml.js',
                'vendor/core/core/base/libraries/codemirror/addon/hint/xml-hint.js',
                'vendor/core/core/base/libraries/codemirror/addon/hint/html-hint.js',
                'vendor/core/core/base/libraries/codemirror/addon/hint/css-hint.js',
                'vendor/core/core/base/libraries/codemirror/addon/hint/javascript-hint.js',
            ];

            break;

        case 'css':
            $js = [
                ...$js,
                'vendor/core/core/base/libraries/codemirror/mode/css.js',
                'vendor/core/core/base/libraries/codemirror/addon/hint/css-hint.js',
            ];

            break;

        case 'javascript':
            $js = [
                ...$js,
                'vendor/core/core/base/libraries/codemirror/mode/javascript.js',
                'vendor/core/core/base/libraries/codemirror/addon/hint/javascript-hint.js',
            ];

            break;
    }

    Assets::addStylesDirectly($css)
        ->addScriptsDirectly($js);
@endphp

<x-core::form-group>
    @if ($label)
        <x-core::form.label
            :label="$label"
            :for="$id"
        />
    @endif

    <textarea
        {{ $attributes->merge(['name' => $name, 'class' => 'form-control', 'id' => $id, 'data-bb-code-editor' => '', 'data-mode' => $mode]) }}
    >{{ $value ?: $slot }}</textarea>

    @if ($helperText)
        <x-core::form.helper-text class="mt-2">{!! $helperText !!}</x-core::form.helper-text>
    @endif

    <x-core::form.error :key="$errorKey" />
</x-core::form-group>

@if (request()->ajax())
    @foreach($css as $cssItem)
        <link rel="stylesheet" href="{{ asset($cssItem) }}">
    @endforeach

    @foreach($js as $jsItem)
        <script src="{{ asset($jsItem) }}"></script>
    @endforeach
@endif
