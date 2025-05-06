<x-core::modal
    id="global-search-modal"
    data-bb-toggle="gs-modal"
    :close-button="false"
    :centered="false"
>
    <x-core::form :url="route('core.global-search')" data-bb-toggle="gs-form">
        <x-core::form.text-input
            name="keyword"
            :label="$name = trans('core/base::base.global_search.search')"
            :label-sr-only="true"
            :placeholder="$name"
            :input-icon="true"
            tabindex="0"
            data-bb-toggle="gs-input"
            wrapper-class="input-icon"
        >
            <x-slot:prepend>
                <span class="input-icon-addon">
                    <x-core::icon name="ti ti-search" />
                </span>
            </x-slot:prepend>
        </x-core::form.text-input>
    </x-core::form>

    <div data-bb-toggle="gs-result">
        <div class="text-center text-muted">
            {{ trans('core/base::base.global_search.no_result') }}
        </div>
    </div>

    <x-slot:footer>
        <span class="text-muted">
            <kbd>
                <x-core::icon name="ti ti-arrow-back" />
            </kbd>
            {{ trans('core/base::base.global_search.to_select') }}
        </span>

        <span class="text-muted">
            <kbd>
                <x-core::icon name="ti ti-arrow-narrow-up" />
            </kbd>
            <kbd>
                <x-core::icon name="ti ti-arrow-narrow-down" />
            </kbd>
            {{ trans('core/base::base.global_search.to_navigate') }}
        </span>

        <span class="text-muted">
            <kbd>esc</kbd>
            {{ trans('core/base::base.global_search.to_close') }}
        </span>
    </x-slot:footer>
</x-core::modal>

<x-core::custom-template id="gs-not-result-template">
    <div class="text-center text-muted">
        {{ trans('core/base::base.global_search.no_result') }}
    </div>
</x-core::custom-template>

<script src="{{ asset('vendor/core/core/base/js/global-search.js') }}"></script>
