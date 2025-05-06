<x-core::form.label>
    {{ str_replace('-', ' ', Str::title(Str::slug($name))) }}
    <small>({{ trans('core/setting::setting.media.default_size_value', ['size' => RvMedia::getConfig('sizes.' . $name)]) }})</small>
</x-core::form.label>
