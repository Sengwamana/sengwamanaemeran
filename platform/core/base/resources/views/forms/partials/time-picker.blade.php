<div class="input-group">
    {!! Form::text(
        $name,
        $value ?? Carbon\Carbon::now()->format('G:i'),
        array_merge($attributes, [
            'class' =>
                Arr::get($attributes, 'class', '') .
                str_replace(Arr::get($attributes, 'class'), '', ' form-control time-picker timepicker timepicker-24'),
        ]),
    ) !!}

    <x-core::button icon="ti ti-clock" :icon-only="true" />
</div>
