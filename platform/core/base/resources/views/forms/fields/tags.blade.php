<x-core::form.field
    :showLabel="$showLabel"
    :showField="$showField"
    :options="$options"
    :name="$name"
    :prepend="$prepend ?? null"
    :append="$append ?? null"
    :showError="$showError"
    :nameKey="$nameKey"
>
    @php
        if (Arr::get($options, 'choices')) {
            $classAppend = 'list-tagify';
        } else {
            $classAppend = 'tags';
        }

        $options['attr']['class'] = (rtrim(Arr::get($options, 'attr.class'), ' ') ?: '')  . ' ' . $classAppend;

        if (Arr::has($options, 'choices')) {
            $choices = $options['choices'];

            if ($choices instanceof \Illuminate\Support\Collection) {
                $choices = $choices->toArray();
            }

            if ($choices) {
                $options['attr']['data-list'] = json_encode($choices);
            }
        }

        if (Arr::has($options, 'selected')) {
            $options['value'] = $options['selected'];
        }
    @endphp

    <x-slot:label>
        @if ($showLabel && $options['label'] !== false && $options['label_show'])
            {!! Form::customLabel($name, $options['label'], $options['label_attr']) !!}
        @endif
    </x-slot:label>

    {!! Form::text($name, $options['value'], $options['attr']) !!}
</x-core::form.field>
