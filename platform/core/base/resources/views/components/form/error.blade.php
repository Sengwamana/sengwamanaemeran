@props([
    'key' => null,
])

@error($key)
    <div {{ $attributes->merge(['class' => 'invalid-feedback']) }}>
        {{ $message }}
    </div>
@enderror
