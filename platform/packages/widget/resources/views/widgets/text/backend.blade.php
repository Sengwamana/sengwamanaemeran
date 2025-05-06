<div class="mb-3">
    <label for="widget-name">{{ trans('core/base::forms.name') }}</label>
    <input
        class="form-control"
        name="name"
        type="text"
        value="{{ $config['name'] }}"
    >
</div>
<div class="mb-3">
    <label for="content">{{ trans('core/base::forms.content') }}</label>
    <textarea
        class="form-control"
        name="content"
        rows="7"
    >{{ $config['content'] }}</textarea>
</div>
