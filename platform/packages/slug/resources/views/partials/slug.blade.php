@php
    $name = 'slug';
    $options = [
        'prefix' => SlugHelper::getPrefix($object::class),
        'model' => $object,
    ];
@endphp

@include('packages/slug::forms.fields.permalink')
