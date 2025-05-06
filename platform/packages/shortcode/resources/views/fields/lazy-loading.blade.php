<div class="mb-3 position-relative">
    <label for="enable_lazy_loading" class="form-label">{{ __('Enable lazy loading') }}</label>

    {!! Form::customSelect('enable_lazy_loading', ['no' => __('No'), 'yes' => __('Yes')], Arr::get($attributes, 'enable_lazy_loading', 'no')) !!}

    {!! Form::helper(__('When enabled, shortcode content will be loaded sequentially as the page loads, rather than all at once. This can help improve page load times.')) !!}
</div>
