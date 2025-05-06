<x-core::form.text-input
    :label="trans('core/base::forms.name')"
    name="name"
    :value="$config['name']"
/>

<div class="pt-3 border-top">
    <h4 class="fs-3">{{ trans('core/base::forms.content') }}</h4>

    {!! Form::repeater('items', $config['items'], $fields) !!}
</div>
