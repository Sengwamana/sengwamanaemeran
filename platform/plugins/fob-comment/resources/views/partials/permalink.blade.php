<div class="mb-3">
    <label class="form-label d-inline-block mb-0">{{ trans('plugins/fob-comment::comment.permalink') }}:</label>
    <a href="{{ $model->reference_url }}#comment-{{ $model->getKey() }}" target="_blank">{{ $model->reference_url }}</a>
</div>
