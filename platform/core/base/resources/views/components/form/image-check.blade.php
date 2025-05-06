@props([
    'name' => null,
    'options' => [],
    'value' => null,
])

@php
    $id = sprintf('%s-%s', $attributes->get('id', $name), uniqid());
    $numberItemsPerRow = 3;
    $imagePaddingTop = null;

    if ($numberItemsPerRowAttr = Arr::get($attributes, 'number_items_per_row')) {
        $numberItemsPerRow = $numberItemsPerRowAttr;
    }

    if (! ($isWithoutAspectRatio = Arr::get($attributes, 'without_aspect_ratio'))) {
        $ratio = null;

        if (Arr::has($attributes, 'ratio')) {
            $ratio = Arr::get($attributes, 'ratio');
        }

        if ($ratio) {
            $imagePaddingTop = match ($ratio) {
                '1:1' => 100,
                '3:4' => 75,
                '4:3' => 125,
                '16:9' => 56.25,
                '9:16' => 178,
                '16:10' => 62.5,
                '10:16' => 160,
                default => null,
            };
        }
    }

    $col =  min($numberItemsPerRow, 12);
@endphp

<div class="row g-2 row-cols-sm-{{ intval(round($col / 2)) }} row-cols-md-{{ $col }}">
    @foreach($options as $key => $option)
        @php
            $label = Arr::get($option, 'label');
            $image = Arr::get($option, 'image', asset('vendor/core/core/base/images/ui-selector-placeholder.jpg'));
        @endphp

        <div @class(['ui-selector mb-3', 'without-ratio' => $isWithoutAspectRatio])>
            <label for="{{ $id }}-{{ $key }}" class="form-imagecheck form-imagecheck-tick mb-2">
                <input {{ $attributes->merge(['id' => "$id-$key", 'name' => $name, 'type' => 'radio', 'value' => $key, 'class' => 'form-imagecheck-input', 'checked' => $key == old($name, $value)]) }}>
                <span class="form-imagecheck-figure" @style(["padding-top:$imagePaddingTop%" => $imagePaddingTop])>
                    <img src="{{ $image }}" alt="{{ $label }}" class="form-imagecheck-image">
                </span>
            </label>

            @if($label)
                <label for="{{ $id }}-{{ $key }}" class="cursor-pointer text-center form-check-label">
                    {{ $label }}
                </label>
            @endif
        </div>
    @endforeach
</div>
