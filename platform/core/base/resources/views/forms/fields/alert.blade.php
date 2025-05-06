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
    <x-core::alert
        :attributes="new Illuminate\View\ComponentAttributeBag((array) $options['attr'])"
        :type="$options['type']">
        {!! $options['html'] !!}
    </x-core::alert>
</x-core::form.field>
