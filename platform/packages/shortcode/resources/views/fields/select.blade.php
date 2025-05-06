<select
    class="select-full"
    name="{{ $field }}"
    @if ($multiple) multiple @endif
>
    @foreach ($options as $key => $label)
        <option
            value="{{ $key }}"
            @selected(in_array($key, $value))
        >{{ $label }}</option>
    @endforeach
</select>
