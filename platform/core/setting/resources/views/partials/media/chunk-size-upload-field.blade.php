<x-core-setting::form-group>
    <x-core::form.on-off.checkbox
        name="media_chunk_enabled"
        :label="trans('core/setting::setting.media.enable_chunk')"
        :checked="RvMedia::isChunkUploadEnabled()"
        :helper-text="trans('core/setting::setting.enable_chunk_description')"
        data-bb-toggle="collapse"
        data-bb-target=".chunk-size"
    />

    <x-core::form.fieldset
        data-bb-value="1"
        class="chunk-size"
        @style(['display: none;' => !RvMedia::isChunkUploadEnabled()])
    >
        <div class="row">
            <div class="col-lg-6">
                <x-core::form.text-input
                    name="media_chunk_size"
                    :label="trans('core/setting::setting.media.chunk_size')"
                    type="number"
                    :value="setting('media_chunk_size', RvMedia::getConfig('chunk.chunk_size'))"
                    :placeholder="trans('core/setting::setting.media.chunk_size_placeholder')"
                />
            </div>
            <div class="col-lg-6">
                <x-core::form.text-input
                    name="media_max_file_size"
                    :label="trans('core/setting::setting.media.max_file_size')"
                    type="number"
                    :value="setting('media_max_file_size', RvMedia::getConfig('chunk.max_file_size'))"
                    :placeholder="trans('core/setting::setting.media.max_file_size_placeholder')"
                />
            </div>
        </div>
    </x-core::form.fieldset>
</x-core-setting::form-group>
