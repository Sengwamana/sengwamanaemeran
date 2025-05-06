<div
    class="modal modal-blur fade media-modal rv-media-modal"
    id="rv_media_modal"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>
    <div
        class="modal-dialog modal-dialog-centered modal-full"
        role="document"
    >
        <div class="modal-content bb-loading">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('core/media::media.gallery') }}</h5>
                <x-core::modal.close-button />
            </div>
            <div
                class="p-0 modal-body media-modal-body media-modal-loading"
                id="rv_media_body"
            >
                <x-core::loading />
            </div>
        </div>
    </div>
</div>

<x-core::modal
    id="image-picker-add-from-url"
    :title="trans('core/media::media.add_from_url')"
    :form-action="route('media.download_url')"
    :form-attrs="['id' => 'image-picker-add-from-url-form']"
>
    <input type="hidden" name="image-box-target">

    <x-core::form.text-input
        :label="trans('core/media::media.url')"
        type="url"
        name="url"
        placeholder="https://"
        :required="true"
    />

    <x-core::form.checkbox
        :label="trans('core/media::media.download_image_to_local_storage')"
        name="download_image_to_local_storage"
        value="1"
        id="download_image_to_local_storage"
        :checked="true"
    >
        <x-slot:helper-text>
            {{ trans('core/media::media.download_image_to_local_storage_helper') }}
        </x-slot:helper-text>
    </x-core::form.checkbox>

    <x-slot:footer>
        <x-core::button
            type="button"
            data-bs-dismiss="modal"
        >
            {{ trans('core/base::forms.cancel') }}
        </x-core::button>

        <x-core::button
            type="submit"
            color="primary"
            data-bb-toggle="image-picker-add-from-url"
            form="image-picker-add-from-url-form"
        >
            {{ trans('core/base::forms.save_and_continue') }}
        </x-core::button>
    </x-slot:footer>
</x-core::modal>

@include('core/media::config')

<script src="{{ asset('vendor/core/core/media/js/integrate.js?v=' . time()) }}"></script>

{!! apply_filters('core_base_media_after_assets', null) !!}
