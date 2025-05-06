<x-core::form.image
    :name="$name"
    :value="$value"
    action="select-image"
    :attributes="new Illuminate\View\ComponentAttributeBag((array) Arr::get($attributes, 'attr', []))"
/>
