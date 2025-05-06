@php
    $shortcodes = Shortcode::getAll()
@endphp

<script>
    window.BB_SHORTCODES = {!!
        Js::from(
            collect($shortcodes)->mapWithKeys(
            fn( $shortcode, $key) => [$key => ! empty($shortcode['name']) ? $shortcode['name'] : (! empty($shortcode['description']) ?: $key)]
            )->toArray()
        )
    !!}
</script>

<x-core::modal
    :title="trans('packages/shortcode::shortcode.ui-blocks')"
    id="shortcode-list-modal"
    class="shortcode-list-modal"
    size="full"
    :scrollable="true"
>
    <x-core::form.text-input
        type="search"
        name="search"
        :placeholder="trans('packages/shortcode::shortcode.search')"
        :group-flat="true"
    >
        <x-slot:prepend>
            <span class="input-group-text">
                <x-core::icon name="ti ti-search" />
            </span>
        </x-slot:prepend>

        <x-slot:append>
            <button type="button" class="input-group-text" data-bb-toggle="shortcode-clear-search">
                <x-core::icon name="ti ti-x" />
            </button>
        </x-slot:append>
    </x-core::form.text-input>

    <div class="row shortcode-list">
        @foreach ($shortcodes as $key => $shortcode)
            @continue(! isset($shortcode['name']))
            <div class="col-xl-3 col-lg-4 col-sm-6 mb-3">
                <label
                    class="shortcode-item-wrapper w-100"
                    data-bb-toggle="shortcode-select"
                    data-name="{{ $shortcode['name'] }}"
                    data-url="{{ route('short-codes.ajax-get-admin-config', $key) }}"
                    data-description="{{ $shortcode['description'] }}"
                    href="{{ route('short-codes.ajax-get-admin-config', $key) }}"
                    data-key="{{ $key }}"
                    for="shortcode-item-{{ $loop->index }}"
                >
                    <input class="d-none shortcode-item-input" id="shortcode-item-{{ $loop->index }}" value="{{ $loop->index }}" type="radio" name="shortcode_name" data-bb-toggle="shortcode-item-radio">
                    <div class="shortcode-item">
                        <x-core::card>
                            <div class="image-wrapper w-100 position-relative overflow-hidden">
                                <img src="{{ $image = Arr::get($shortcode, 'previewImage') ?: asset('vendor/core/packages/shortcode/images/placeholder-code.jpg') }}" alt="{{ $shortcode['name'] }}"/>
                            </div>

                            <x-core::card.header>
                                <div class="w-100">
                                    <x-core::card.title class="mb-1" title="{{ $shortcode['name'] }}">
                                        {{ $shortcode['name'] }}
                                    </x-core::card.title>

                                    <div class="row align-items-center">
                                        <x-core::card.subtitle class="col-9" title="{{ $shortcode['description'] }}">
                                            {{ $shortcode['description'] }}
                                        </x-core::card.subtitle>

                                        <div class="col-3 text-end">
                                            <x-core::button size="xs" class="use-button" data-bb-toggle="shortcode-button-use">
                                                {{ trans('packages/shortcode::shortcode.use') }}
                                            </x-core::button>
                                        </div>
                                    </div>
                                </div>
                            </x-core::card.header>
                        </x-core::card>
                    </div>
                </label>
            </div>
        @endforeach
    </div>

    <x-core::empty-state
        :title="trans('packages/shortcode::shortcode.no_shortcode_found')"
        style="display: none;"
        class="shortcode-empty"
    />

    <x-slot:footer>
        <div class="btn-list">
            <x-core::button
                data-bs-dismiss="modal"
            >
                {{ trans('core/base::base.close') }}
            </x-core::button>

            <x-core::button
                color="primary"
                data-bb-toggle="shortcode-use"
                disabled
            >
                {{ trans('packages/shortcode::shortcode.use') }}
            </x-core::button>
        </div>
    </x-slot:footer>
</x-core::modal>

<x-core::modal
    :title="trans('core/base::forms.add_short_code')"
    id="shortcode-modal"
    class="shortcode-modal"
    :scrollable="true"
    data-bs-backdrop="static"
>
    <form class="shortcode-data-form">
        <input
            type="hidden"
            class="shortcode-input-key"
        >
        <div class="shortcode-admin-config short-code-admin-config"></div>
    </form>

    <x-slot:footer>
        <x-core::button
            data-bs-dismiss="modal"
        >
            {{ trans('core/base::tables.cancel') }}
        </x-core::button>
        <x-core::button
            color="primary"
            data-bb-toggle="shortcode-add-single"
            :data-add-text="trans('core/base::forms.add')"
            :data-update-text="trans('core/base::forms.update')"
        >
            {{ trans('core/base::forms.add') }}
        </x-core::button>
    </x-slot:footer>
</x-core::modal>
