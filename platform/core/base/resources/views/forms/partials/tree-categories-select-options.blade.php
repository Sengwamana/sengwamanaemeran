@if($categories)
    @php
        $selected = (array) $selected;
    @endphp

    @foreach($categories as $category)
        <option
            value="{{ $category->id ?? '' }}"
            @selected(in_array($category->id, $selected))
            data-render-item="{{ $category->name }}"
            data-render-option="{{ $indent }} {{ $category->name }}"
        >
            {{ $category->name }}
        </option>

        @if($category->activeChildren)
            @include('core/base::forms.partials.tree-categories-select-options', [
                'categories' => $category->activeChildren,
                'selected' => $selected,
                'currentId' => $currentId,
                'name' => $name,
                'indent' => "{$indent}—"
            ])
        @endif
    @endforeach
@endif
