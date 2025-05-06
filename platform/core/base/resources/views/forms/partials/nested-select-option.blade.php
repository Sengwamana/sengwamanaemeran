@php
    if (!isset($groupedOptions)) {
        if (! $options instanceof \Illuminate\Support\Collection) {
            $options = collect($options);
        }

        $groupedOptions = $options->groupBy('parent_id');
    }

    $currentOptions = $groupedOptions->get($parentId = $parentId ?? 0);
@endphp

@if($currentOptions)
    @foreach ($currentOptions as $option)
        <option value="{{ $option->id }}" @selected(is_array($selected) ? in_array($option->id, $selected) : $option->id == $selected)>{!! $indent !!}{{ $option->name }}</option>

        @if ($groupedOptions->has($option->id))
            @include('core/base::forms.partials.nested-select-option', [
                'options' => $groupedOptions,
                'indent' => $indent . '&nbsp;&nbsp;',
                'parentId' => $option->id,
                'selected' => $selected,
            ])
        @endif
    @endforeach
@endif
