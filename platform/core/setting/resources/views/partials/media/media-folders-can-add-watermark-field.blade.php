<div class="row">
    <div class="col-lg-12">
        <x-core-setting::form-group>
            <x-core::form.label
                for="media_folders_can_add_watermark"
                :label="trans('core/setting::setting.media.media_folders_can_add_watermark')"
            />
            <x-core::form.fieldset class="mt-3">
                <div class="multi-check-list-wrapper">
                    <x-core-setting::form-group>
                        <x-core::form.checkbox
                            :label="trans('core/setting::setting.media.all')"
                            class="check-all"
                            data-set=".media-folder"
                            name="media_folders_can_add_watermark_all"
                            :checked="empty($folderIds) || count($folderIds) === count($folders)"
                        >
                            <x-slot:helper-text>
                                {{ trans('core/setting::setting.media.all_helper_text') }}
                            </x-slot:helper-text>
                        </x-core::form.checkbox>
                    </x-core-setting::form-group>

                    @foreach ($folders as $key => $item)
                        <x-core-setting::form-group @class(['mb-n3' => $loop->last])>
                            <x-core::form.checkbox
                                :label="$item"
                                class="media-folder"
                                name="media_folders_can_add_watermark[]"
                                value="{{ $key }}"
                                id="media-folder-item-{{ $key }}"
                                    :checked="empty($folderIds) || in_array($key, $folderIds)"
                            />
                        </x-core-setting::form-group>
                    @endforeach
                </div>
            </x-core::form.fieldset>
        </x-core-setting::form-group>

    </div>
</div>
