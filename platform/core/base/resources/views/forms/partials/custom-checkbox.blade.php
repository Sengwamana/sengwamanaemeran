@if (sizeof($values = (array) $values) > 1)
    <div class="mt-checkbox-list">
@endif
@foreach ($values as $value)
    <x-core::form.checkbox
        :name="$value[0] ?? ''"
        :value="$value[1] ?? ''"
        :label="BaseHelper::clean($value[2] ?? '')"
        :checked="$value[3] ?? false"
        :disabled="$value[4] ?? false"
        :$inline
    />
@endforeach
@if (sizeof($values) > 1)
    </div>
@endif
