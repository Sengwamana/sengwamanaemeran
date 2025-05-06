<x-core::form.field
    :showLabel="false"
    :showField="$showField"
    :options="$options"
    :name="$name"
    :prepend="$prepend ?? null"
    :append="$append ?? null"
    :showError="$showError"
    :nameKey="$nameKey"
>
    @php
        if (isset($options['label_attr'])) {
            $options['attr']['label_attr'] = $options['label_attr'];
        }
    @endphp

    <x-core::form.checkbox
        :label="($showLabel && $options['label'] !== false && $options['label_show']) ? $options['label'] : null"
        :name="$name"
        :value="$options['value']"
        :checked="$options['checked'] ?? $options['value']"
        :attributes="new Illuminate\View\ComponentAttributeBag((array) $options['attr'])"
    />
</x-core::form.field>
