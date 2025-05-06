@if ($categories)
    @php
        $selected = (array) $selected;
    @endphp

    <ul @class(['list-unstyled', $class ?? null])>
        @foreach ($categories as $category)
            @continue($category->id === $currentId)

            <li>
                <x-core::form.checkbox
                    :label="$category->name"
                    :name="$name"
                    :value="$category->id"
                    :checked="in_array($category->id, $selected)"
                />

                @if ($category->activeChildren->isNotEmpty())
                    @include('core/base::forms.partials.tree-categories-checkbox-options', [
                        'categories' => $category->activeChildren,
                        'selected' => $selected,
                        'currentId' => $currentId,
                        'name' => $name,
                        'class' => 'ms-4 mt-2'
                    ])
                @endif
            </li>
        @endforeach
    </ul>
@endif
