@php
    $model = (object)$options['model'];
    $options['prefix'] = SlugHelper::getPrefix($model::class);
@endphp

<input
    type="hidden"
    name="model"
    value="{{ $model::class }}"
>

@if (empty($model))
    <div class="mb-3 @if ($errors->has($name)) has-error @endif">
        {!! Form::permalink($name, old($name), 0, $options['prefix'], [], true, $model) !!}
        {!! Form::error($name, $errors) !!}
    </div>
@else
    <div class="mb-3 @if ($errors->has($name)) has-error @endif">
        {!! Form::permalink(
            $name,
            $model->slug,
            $model->slug_id,
            $options['prefix'],
            SlugHelper::canPreview($model) && in_array($model->status, [Botble\Base\Enums\BaseStatusEnum::DRAFT, Botble\Base\Enums\BaseStatusEnum::PENDING]),
            [],
            true,
            $model,
        ) !!}
        {!! Form::error($name, $errors) !!}
    </div>
@endif
