<x-core::form.field
    :showLabel="false"
    :showField="$showField"
    :options="$options"
    :name="$name"
    :showError="$showError"
    :nameKey="$nameKey"
>
    {!! Form::hidden($name, $options['value'], $options['attr']) !!}
</x-core::form.field>
