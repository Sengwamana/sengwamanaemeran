@if (!Arr::get($attributes, 'without-buttons', false))
    @php
        $id = Arr::get($attributes, 'id', $name);
        Arr::set($attributes, 'id', $id);
    @endphp

    <div class="mb-2 btn-list">
        <x-core::button
            type="button"
            data-result="{{ $id }}"
            class="show-hide-editor-btn"
        >
            {{ trans('core/base::forms.show_hide_editor') }}
        </x-core::button>

        <x-core::button
            type="button"
            icon="ti ti-photo"
            class="btn_gallery"
            data-result="{{ $id }}"
            data-multiple="true"
            data-action="media-insert-{{ BaseHelper::getRichEditor() }}"
        >
            {{ trans('core/media::media.add') }}
        </x-core::button>

        {!! apply_filters(BASE_FILTER_FORM_EDITOR_BUTTONS, null, $attributes, $id) !!}
    </div>

    @push('header')
        {!! apply_filters(BASE_FILTER_FORM_EDITOR_BUTTONS_HEADER, null, $attributes, $id) !!}
    @endpush

    @push('footer')
        {!! apply_filters(BASE_FILTER_FORM_EDITOR_BUTTONS_FOOTER, null, $attributes, $id) !!}
    @endpush
@else
    @php Arr::forget($attributes, 'with-short-code'); @endphp
@endif

{!! call_user_func_array([Form::class, BaseHelper::getRichEditor()], [$name, $value, $attributes]) !!}
