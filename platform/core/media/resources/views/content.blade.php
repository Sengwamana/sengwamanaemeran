<div class="rv-media-container">
    <x-core::card class="rv-media-wrapper">
        <input
            type="checkbox"
            id="media_details_collapse"
            class="d-none fake-click-event"
        >

        <x-core::offcanvas
            id="rv-media-aside"
            @class(['d-md-none' => RvMedia::getConfig('sidebar_display') !== 'vertical'])
            style="--bb-offcanvas-width: 85%"
        >
            <x-core::offcanvas.header>
                <x-core::offcanvas.title>
                    {{ trans('core/media::media.menu_name') }}
                </x-core::offcanvas.title>
                <x-core::offcanvas.close-button />
            </x-core::offcanvas.header>

            <x-core::offcanvas.body class="p-0">
                <x-core::list-group :flush="true">
                    <x-core::list-group.header>
                        {{ trans('core/media::media.filter') }}
                    </x-core::list-group.header>
                    <x-core::list-group.item
                        :action="true"
                        class="js-rv-media-change-filter"
                        data-type="filter"
                        data-value="everything"
                    >
                        <x-core::icon name="ti ti-recycle" />
                        {{ trans('core/media::media.everything') }}
                    </x-core::list-group.item>

                    @if (array_key_exists('image', RvMedia::getConfig('mime_types', [])))
                        <x-core::list-group.item
                            :action="true"
                            class="js-rv-media-change-filter"
                            data-type="filter"
                            data-value="video"
                        >
                            <x-core::icon name="ti ti-photo" />
                            {{ trans('core/media::media.image') }}
                        </x-core::list-group.item>
                    @endif

                    @if (array_key_exists('video', RvMedia::getConfig('mime_types', [])))
                        <x-core::list-group.item
                            :action="true"
                            class="js-rv-media-change-filter"
                            data-type="filter"
                            data-value="document"
                        >
                            <x-core::icon name="ti ti-video" />
                            {{ trans('core/media::media.video') }}
                        </x-core::list-group.item>
                    @endif

                    <x-core::list-group.item
                        :action="true"
                        class="js-rv-media-change-filter"
                        data-type="filter"
                        data-value="image"
                    >
                        <x-core::icon name="ti ti-file" />
                        {{ trans('core/media::media.document') }}
                    </x-core::list-group.item>
                </x-core::list-group>

                <x-core::list-group :flush="true">
                    <x-core::list-group.header>
                        {{ trans('core/media::media.view_in') }}
                    </x-core::list-group.header>
                    <x-core::list-group.item
                        :action="true"
                        class="js-rv-media-change-filter"
                        data-type="view_in"
                        data-value="all_media"
                    >
                        <x-core::icon name="ti ti-world" />
                        {{ trans('core/media::media.all_media') }}
                    </x-core::list-group.item>

                    @if (RvMedia::hasAnyPermission(['folders.destroy', 'files.destroy']))
                        <x-core::list-group.item
                            :action="true"
                            class="js-rv-media-change-filter"
                            data-type="view_in"
                            data-value="trash"
                        >
                            <x-core::icon name="ti ti-trash" />
                            {{ trans('core/media::media.trash') }}
                        </x-core::list-group.item>
                    @endif

                    <x-core::list-group.item
                        :action="true"
                        class="js-rv-media-change-filter"
                        data-type="view_in"
                        data-value="recent"
                    >
                        <x-core::icon name="ti ti-clock" />
                        {{ trans('core/media::media.recent') }}
                    </x-core::list-group.item>

                    <x-core::list-group.item
                        :action="true"
                        class="js-rv-media-change-filter"
                        data-type="view_in"
                        data-value="favorites"
                    >
                        <x-core::icon name="ti ti-star" />
                        {{ trans('core/media::media.favorites') }}
                    </x-core::list-group.item>
                </x-core::list-group>
            </x-core::offcanvas.body>
        </x-core::offcanvas>

        <div class="rv-media-main-wrapper">
            <x-core::card.header class="flex-column rv-media-header p-0">
                <div class="w-100 p-2 rv-media-top-header flex-wrap gap-3 d-flex justify-content-between align-items-start border-bottom bg-body">
                    <div class="d-flex gap-2 justify-content-between w-100 w-md-auto rv-media-actions">
                        <x:core::button
                            class="d-flex d-md-none"
                            icon="ti ti-menu-2"
                            :icon-only="true"
                            data-bs-toggle="offcanvas"
                            href="#rv-media-aside"
                        />

                        <div class="btn-list">
                            @if (RvMedia::hasPermission('files.create'))
                                <x-core::dropdown
                                    :label="trans('core/media::media.upload')"
                                    icon="ti ti-upload"
                                    color="primary"
                                >
                                    <x-core::dropdown.item
                                        :label="trans('core/media::media.upload_from_local')"
                                        class="js-dropzone-upload dropdown-item"
                                        icon="ti ti-upload"
                                    />

                                    <x-core::dropdown.item
                                        :label="trans('core/media::media.upload_from_url')"
                                        class="js-download-action dropdown-item"
                                        icon="ti ti-link"
                                    />
                                </x-core::dropdown>
                            @endif

                            @if (RvMedia::hasPermission('folders.create'))
                                <x-core::button
                                    type="button"
                                    color="primary"
                                    :tooltip="trans('core/media::media.create_folder')"
                                    class="js-create-folder-action"
                                    icon="ti ti-folder-plus"
                                    :icon-only="true"
                                />
                            @endif

                            <x-core::button
                                type="button"
                                color="primary"
                                :tooltip="trans('core/media::media.refresh')"
                                class="js-change-action"
                                icon="ti ti-refresh"
                                :icon-only="true"
                                data-type="refresh"
                            />

                            @if (RvMedia::getConfig('sidebar_display') !== 'vertical')
                                <x-core::dropdown wrapper-class="d-none d-md-block">
                                    <x-slot:trigger>
                                        <x-core::button
                                            type="button"
                                            color="primary"
                                            icon="ti ti-filter"
                                            class="dropdown-toggle js-rv-media-change-filter-group js-filter-by-type"
                                            data-bs-toggle="dropdown"
                                            :tooltip="trans('core/media::media.filter')"
                                        >
                                            <span class="js-rv-media-filter-current"></span>
                                        </x-core::button>
                                    </x-slot:trigger>

                                    <x-core::dropdown.item
                                        :label="trans('core/media::media.everything')"
                                        icon="ti ti-recycle"
                                        class="js-rv-media-change-filter"
                                        data-type="filter"
                                        data-value="everything"
                                    />

                                    @if (array_key_exists('image', RvMedia::getConfig('mime_types', [])))
                                        <x-core::dropdown.item
                                            :label="trans('core/media::media.image')"
                                            icon="ti ti-photo"
                                            class="js-rv-media-change-filter"
                                            data-type="filter"
                                            data-value="image"
                                        />
                                    @endif

                                    @if (array_key_exists('video', RvMedia::getConfig('mime_types', [])))
                                        <x-core::dropdown.item
                                            :label="trans('core/media::media.video')"
                                            icon="ti ti-video"
                                            class="js-rv-media-change-filter"
                                            data-type="filter"
                                            data-value="video"
                                        />
                                    @endif

                                    <x-core::dropdown.item
                                        :label="trans('core/media::media.document')"
                                        icon="ti ti-file"
                                        class="js-rv-media-change-filter"
                                        data-type="filter"
                                        data-value="document"
                                    />
                                </x-core::dropdown>

                                <x-core::dropdown wrapper-class="d-none d-md-block">
                                    <x-slot:trigger>
                                        <x-core::button
                                            type="button"
                                            color="primary"
                                            icon="ti ti-eye"
                                            class="dropdown-toggle js-rv-media-change-filter-group js-filter-by-view-in"
                                            data-bs-toggle="dropdown"
                                            :tooltip="trans('core/media::media.view_in')"
                                        >
                                            <span class="js-rv-media-filter-current"></span>
                                        </x-core::button>
                                    </x-slot:trigger>

                                    <x-core::dropdown.item
                                        :label="trans('core/media::media.all_media')"
                                        icon="ti ti-world"
                                        class="js-rv-media-change-filter"
                                        data-type="view_in"
                                        data-value="all_media"
                                    />

                                    <x-core::dropdown.item
                                        :label="trans('core/media::media.trash')"
                                        icon="ti ti-trash"
                                        class="js-rv-media-change-filter"
                                        data-type="view_in"
                                        data-value="trash"
                                    />

                                    <x-core::dropdown.item
                                        :label="trans('core/media::media.recent')"
                                        icon="ti ti-clock"
                                        class="js-rv-media-change-filter"
                                        data-type="view_in"
                                        data-value="recent"
                                    />

                                    <x-core::dropdown.item
                                        :label="trans('core/media::media.favorites')"
                                        icon="ti ti-star"
                                        class="js-rv-media-change-filter"
                                        data-type="view_in"
                                        data-value="favorites"
                                    />
                                </x-core::dropdown>
                            @endif

                            @if (RvMedia::hasAnyPermission(['folders.destroy', 'files.destroy']))
                                <x-core::button
                                    type="button"
                                    color="danger"
                                    class="d-none js-files-action"
                                    data-action="empty_trash"
                                    icon="ti ti-trash"
                                >
                                    {{ trans('core/media::media.empty_trash') }}
                                </x-core::button>
                            @endif
                        </div>
                    </div>
                    <div class="rv-media-search">
                        <form
                            class="input-search-wrapper"
                            action=""
                            method="GET"
                        >
                            <div class="input-group">
                                <input
                                    type="search"
                                    class="form-control"
                                    name="search"
                                    placeholder="{{ trans('core/media::media.search_file_and_folder') }}"
                                />
                                <x-core::button
                                    type="submit"
                                    icon="ti ti-search"
                                    :icon-only="true"
                                />
                            </div>
                        </form>
                    </div>
                </div>
                <div class="w-100 d-flex flex-wrap gap-3 p-2">
                    <div class="d-flex w-100 w-md-auto align-items-center rv-media-breadcrumb">
                        <ul class="breadcrumb"></ul>
                    </div>
                    <div class="d-flex justify-content-between justify-content-md-end align-items-center rv-media-tools w-100 w-md-auto">
                        <div
                            class="btn-list"
                            role="group"
                        >
                            <x-core::dropdown
                                :label="trans('core/media::media.sort')"
                                icon="ti ti-sort-a-z"
                            >
                                @foreach ($sorts as $key => $item)
                                    <x-core::dropdown.item
                                        :label="$item['label']"
                                        :icon="$item['icon']"
                                        class="js-rv-media-change-filter"
                                        data-type="sort_by"
                                        :data-value="$key"
                                    />
                                @endforeach
                            </x-core::dropdown>

                            <x-core::dropdown
                                :label="trans('core/media::media.actions')"
                                icon="ti ti-hand-finger"
                                wrapper-class="rv-dropdown-actions"
                                :disabled="true"
                            />
                        </div>
                        <div
                            class="btn-group js-rv-media-change-view-type ms-2"
                            role="group"
                        >
                            <x-core::button
                                type="button"
                                data-type="tiles"
                                icon="ti ti-layout-grid"
                                :icon-only="true"
                            />
                            <x-core::button
                                type="button"
                                data-type="list"
                                icon="ti ti-layout-list"
                                :icon-only="true"
                            />
                        </div>
                        <x-core::button
                            tag="label"
                            for="media_details_collapse"
                            class="collapse-panel ms-2 d-none d-sm-flex"
                            icon="ti ti-arrow-bar-right"
                            :icon-only="true"
                        />
                    </div>
                </div>
            </x-core::card.header>

            <main class="rv-media-main">
                <div class="rv-media-items"></div>
                <div class="rv-media-details" style="display: none">
                    <div class="rv-media-thumbnail">
                        <x-core::icon name="ti ti-photo" />
                    </div>
                    <div class="rv-media-description">
                        <div class="rv-media-name">
                            <p>{{ trans('core/media::media.nothing_is_selected') }}</p>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="d-none rv-media-footer">
                <x-core::button
                    type="button"
                    color="primary"
                    class="js-insert-to-editor"
                >
                    {{ trans('core/media::media.insert') }}
                </x-core::button>
            </footer>
        </div>
        <div class="rv-upload-progress hide-the-pane position-fixed bottom-0 end-0 ">
            <x-core::card>
                <x-core::card.header class="position-relative">
                    <h3 class="panel-title mb-0">{{ trans('core/media::media.upload_progress') }}</h3>
                    <x-core::button
                        class="close-pane position-absolute top-50 bg-primary text-white text-center p-0"
                    >
                        <x-core::icon
                            class="m-0"
                            name="ti ti-x"
                        />
                    </x-core::button>
                </x-core::card.header>
                <div
                    class="table-responsive overflow-auto"
                    style="max-height: 180px"
                >
                    <x-core::table>
                        <x-core::table.body class="rv-upload-progress-table"></x-core::table.body>
                    </x-core::table>
                </div>
            </x-core::card>
        </div>
    </x-core::card>
</div>

<x-core::modal
    id="modal_add_folder"
    :title="trans('core/media::media.create_folder')"
    :has-form="true"
    :form-attrs="['class' => 'rv-form form-add-folder']"
>
    <x-core::form.text-input
        name="name"
        type="text"
        :placeholder="trans('core/media::media.folder_name')"
    >
        <x-slot:append>
            <x-core::button
                type="submit"
                color="primary"
            >
                {{ trans('core/media::media.create') }}
            </x-core::button>
        </x-slot:append>
    </x-core::form.text-input>
    <div class="modal-notice"></div>
</x-core::modal>

<x-core::modal
    id="modal_rename_items"
    :title="trans('core/media::media.rename')"
    :has-form="true"
    :form-attrs="['class' => 'form-rename']"
>
    <div class="rename-items"></div>
    <div class="modal-notice"></div>

    <x-slot:footer>
        <x-core::button data-bs-dismiss="modal">
            {{ trans('core/media::media.close') }}
        </x-core::button>
        <x-core::button type="submit" color="primary">
            {{ trans('core/media::media.save_changes') }}
        </x-core::button>
    </x-slot:footer>
</x-core::modal>

<x-core::modal
    id="modal_alt_text_items"
    :title="trans('core/media::media.alt_text')"
    :has-form="true"
    :form-attrs="['class' => 'form-alt-text']"
>
    <div class="alt-text-items"></div>
    <div class="modal-notice"></div>

    <x-slot:footer>
        <x-core::button data-bs-dismiss="modal">
            {{ trans('core/media::media.close') }}
        </x-core::button>
        <x-core::button type="submit" color="primary">
            {{ trans('core/media::media.save_changes') }}
        </x-core::button>
    </x-slot:footer>
</x-core::modal>

<x-core::modal
    id="modal_trash_items"
    :title="trans('core/media::media.move_to_trash')"
    :has-form="true"
    :form-attrs="['class' => 'form-delete-items']"
>
    <p>{{ trans('core/media::media.confirm_trash') }}</p>

    <x-core::form.checkbox
        :label="trans('core/media::media.skip_trash')"
        :helper_text="trans('core/media::media.skip_trash_description')"
        name="skip_trash"
        :checked="false"
        id="skip_trash"
    />

    <div class="modal-notice"></div>

    <x-slot:footer>
        <button
            type="submit"
            class="btn btn-danger"
        >{{ trans('core/media::media.confirm') }}</button>
        <button
            type="button"
            class="btn btn-primary"
            data-bs-dismiss="modal"
        >{{ trans('core/media::media.close') }}</button>
    </x-slot:footer>
</x-core::modal>

<x-core::modal
    id="modal_delete_items"
    :title="trans('core/media::media.confirm_delete')"
    :has-form="true"
    :form-attrs="['class' => 'form-delete-items']"
>
    <p>{{ trans('core/media::media.confirm_delete_description') }}</p>
    <div class="modal-notice"></div>

    <x-slot:footer>
        <button
            type="submit"
            class="btn btn-danger"
        >{{ trans('core/media::media.confirm') }}</button>
        <button
            type="button"
            class="btn btn-primary"
            data-bs-dismiss="modal"
        >{{ trans('core/media::media.close') }}</button>
    </x-slot:footer>
</x-core::modal>

<x-core::modal
    id="modal_empty_trash"
    :title="trans('core/media::media.empty_trash_title')"
    :has-form="true"
    :form-attrs="['class' => 'form-empty-trash']"
>
    <p>{{ trans('core/media::media.empty_trash_description') }}</p>
    <div class="modal-notice"></div>

    <x-slot:footer>
        <button
            type="submit"
            class="btn btn-danger"
        >{{ trans('core/media::media.confirm') }}</button>
        <button
            type="button"
            class="btn btn-primary"
            data-bs-dismiss="modal"
        >{{ trans('core/media::media.close') }}</button>
    </x-slot:footer>
</x-core::modal>

<div
    class="modal modal-blur fade"
    tabindex="-1"
    role="dialog"
    id="modal_download_url"
>
    <div
        class="modal-dialog modal-dialog-centered"
        role="document"
    >
        <div class="modal-content">
            <div class="modal-header">
                <h4
                    class="modal-title"
                    data-downloading="{{ trans('core/media::media.downloading') }}"
                    data-text="{{ trans('core/media::media.download_link') }}"
                >
                    <x-core::icon name="ti ti-download" />
                    {{ trans('core/media::media.download_link') }}
                </h4>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="{{ trans('core/media::media.close') }}"
                >
                </button>
            </div>
            <div class="modal-body">
                <form class="rv-form form-download-url">
                    <div id="download-form-wrapper">
                        <div class="mb-3">
                        <textarea
                            rows="4"
                            name="urls"
                            class="form-control"
                            placeholder="https://example.com/image1.jpg&#10;https://example.com/image2.jpg&#10;https://example.com/image3.jpg&#10;..."
                        ></textarea>

                            <x-core::form.helper-text>
                                {{ trans('core/media::media.download_explain') }}
                            </x-core::form.helper-text>
                        </div>
                    </div>

                    <x-core::button type="submit" color="primary" class="w-100">
                        {{ trans('core/media::media.download_link') }}
                    </x-core::button>
                </form>
                <div
                    class="mt-2 modal-notice"
                    id="modal-notice"
                    style="max-height: 350px;overflow: auto"
                ></div>
            </div>
        </div>
    </div>
</div>
<x-core::modal
    title="{{ trans('core/media::media.crop') }}"
    id="modal_crop_image"
    size="lg"
    :form-attrs="['class' => 'rv-form form-crop']"
    :has-form="true"
>
    <div>
        <input
            type="hidden"
            name="image_id"
        >
        <input
            type="hidden"
            name="crop_data"
        >
        <div class="row">
            <div class="col-lg-9">
                <div class="crop-image"></div>
            </div>
            <div class="col-lg-3">
                <div class="mt-3">
                    <x-core::form.text-input
                        label="{{ trans('core/media::media.cropper.height') }}"
                        name="dataHeight"
                        id="dataHeight"
                    />

                    <x-core::form.text-input
                        label="{{ trans('core/media::media.cropper.width') }}"
                        name="dataWidth"
                        id="dataWidth"
                    />

                    <x-core::form.checkbox
                        :label="trans('core/media::media.cropper.aspect_ratio')"
                        name="aspectRatio"
                        :checked="false"
                        id="aspectRatio"
                    />
                </div>
            </div>
        </div>
    </div>
    <x-slot:footer>
        <x-core::button data-bs-dismiss="modal">
            {{ trans('core/media::media.close') }}
        </x-core::button>

        <x-core::button
            type="submit"
            color="primary"
        >
            {{ trans('core/media::media.crop') }}
        </x-core::button>
    </x-slot:footer>
</x-core::modal>

<x-core::modal
    id="modal-properties"
    :title="trans('core/media::media.properties.name')"
>
    <input type="hidden" name="selected">

    <x-core::form.color-selector
        :label="trans('core/media::media.properties.color_label')"
        name="color"
        :choices="RvMedia::getFolderColors()"
    />

    <x-slot:footer>
        <x-core::button data-bs-dismiss="modal">
            {{ trans('core/media::media.close') }}
        </x-core::button>

        <x-core::button
            type="submit"
            color="primary"
        >
            {{ trans('core/media::media.save_changes') }}
        </x-core::button>
    </x-slot:footer>
</x-core::modal>

<x-core::modal
    id="modal_share_items"
    :title="trans('core/media::media.share')"
>
    <div class="share-items">
        <div class="mb-3">
            {!! Form::label('media-share-type', trans('core/media::media.share_type'), [
                'class' => 'form-label',
            ]) !!}
            {!! Form::customSelect('share_type', [
                    'url' => trans('core/media::media.share_as_url'),
                    'indirect_url' => trans('core/media::media.share_as_indirect_url'),
                    'html' => trans('core/media::media.share_as_html'),
                    'markdown' => trans('core/media::media.share_as_markdown'),
                ], null, [
                'id' => 'media-share-type',
                'data-bb-value' => 'share-type',
            ]) !!}
        </div>

        <div class="mb-3" data-bb-value="results">
            {!! Form::label('media-share-results', trans('core/media::media.share_results'), [
                'class' => 'form-label',
            ]) !!}
            {!! Form::textarea('share_result', '', [
                'id' => 'media-share-results',
                'class' => 'form-control',
                'readonly' => true,
                'data-bb-value' => 'share-result',
            ]) !!}
        </div>

        <div class="mb-0 text-end">
            <x-core::button
                class="btn-icon"
                data-bb-toggle="clipboard"
                data-clipboard-parent="#modal_share_items .share-items"
                data-clipboard-target="[data-bb-value='share-result']"
            >
                <x-core::icon name="ti ti-clipboard" data-clipboard-icon="true" />
                <x-core::icon name="ti ti-check" data-clipboard-success-icon="true" class="text-success d-none" />
            </x-core::button>
        </div>
    </div>
</x-core::modal>

<button class="d-none js-rv-clipboard-temp"></button>

<x-core::custom-template id="rv_media_loading">
    <x-core::loading />
</x-core::custom-template>

<x-core::custom-template id="rv_action_item">
    <x-core::dropdown.item
        class="js-files-action"
        data-action="__action__"
        icon="__icon__"
        label="__name__"
    />
</x-core::custom-template>

<x-core::custom-template id="rv_media_items_list">
    <div class="rv-media-list">
        <ul>
            <li class="no-items">
                <x-core::icon name="ti ti-upload" />
                <h3>Drop files and folders here</h3>
                <p>Or use the upload button above.</p>
            </li>
            <li class="rv-media-list-title up-one-level js-up-one-level" title="{{ trans('core/media::media.up_level') }}">
                <div class="custom-checkbox"></div>
                <div class="rv-media-file-name">
                    <x-core::icon name="ti ti-corner-up-left" />
                    <span>...</span>
                </div>
                <div class="rv-media-file-size"></div>
                <div class="rv-media-created-at"></div>
            </li>
        </ul>
    </div>
</x-core::custom-template>

<x-core::custom-template id="rv_media_items_tiles" class="hidden">
    <div class="rv-media-grid">
        <ul>
            <li class="no-items">
                __noItemIcon__
                <h3>__noItemTitle__</h3>
                <p>__noItemMessage__</p>
            </li>
            <li class="rv-media-list-title up-one-level js-up-one-level">
                <div class="rv-media-item" data-context="__type__" title="{{ trans('core/media::media.up_level') }}">
                    <div class="rv-media-thumbnail">
                        <x-core::icon name="ti ti-corner-up-left" size="lg" />
                    </div>
                    <div class="rv-media-description">
                        <div class="title">...</div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</x-core::custom-template>

<x-core::custom-template id="rv_media_items_list_element">
    <li class="rv-media-list-title js-media-list-title js-context-menu" data-context="__type__" title="__name__" data-id="__id__">
        <div class="custom-checkbox">
            <label>
                <input type="checkbox">
                <span></span>
            </label>
        </div>
        <div class="rv-media-file-name">
            __thumb__
            <span>__name__</span>
        </div>
        <div class="rv-media-file-size">__size__</div>
        <div class="rv-media-created-at">__date__</div>
    </li>
</x-core::custom-template>

<x-core::custom-template id="rv_media_items_tiles_element">
    <li class="rv-media-list-title js-media-list-title js-context-menu" data-context="__type__" data-id="__id__">
        <input type="checkbox" class="hidden">
        <div class="rv-media-item" title="__name__">
            <span class="media-item-selected">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path d="M186.301 339.893L96 249.461l-32 30.507L186.301 402 448 140.506 416 110z"></path>
                </svg>
            </span>
            <div class="rv-media-thumbnail">
                __thumb__
            </div>
            <div class="rv-media-description">
                <div class="title title{{ BaseHelper::stringify(request()->input('file_id')) }}">__name__</div>
            </div>
        </div>
    </li>
</x-core::custom-template>

<x-core::custom-template id="rv_media_upload_progress_item">
    <x-core::table.body.row>
        <x-core::table.body.cell>
            <span class="file-name">__fileName__</span>
            <div class="file-error"></div>
        </x-core::table.body.cell>
        <x-core::table.body.cell>
            <span class="file-size">__fileSize__</span>
        </x-core::table.body.cell>
        <x-core::table.body.cell>
            <span class="file-status text-__status__">__message__</span>
            <span class="progress-percent"></span>
        </x-core::table.body.cell>
    </x-core::table.body.row>
</x-core::custom-template>

<x-core::custom-template id="rv_media_breadcrumb_item">
    <li>
        <a href="#" data-folder="__folderId__" class="text-decoration-none js-change-folder">
            __icon__
            __name__
        </a>
    </li>
</x-core::custom-template>

<x-core::custom-template id="rv_media_rename_item">
    <div class="mb-3">
        <div class="input-group">
            <div class="input-group-text">__icon__</div>
            <input class="form-control" placeholder="__placeholder__" value="__value__">
        </div>
    </div>

    <x-core::form.checkbox
        data-folder-label="{{ trans('core/media::media.rename_physical_folder') }}"
        data-file-label="{{ trans('core/media::media.rename_physical_file') }}"
        label="__label__"
        name="rename_physical_file"
        data-bb-toggle="collapse"
        data-bb-target=".rename-physical-file-warning"
    />

    <x-core::alert type="warning" class="rename-physical-file-warning" style="display: none">
        {{ trans('core/media::media.rename_physical_file_warning') }}
    </x-core::alert>
</x-core::custom-template>

<x-core::custom-template id="rv_media_alt_text_item">
    <div class="mb-3">
        <div class="input-group">
            <div class="input-group-text">
                __icon__
            </div>
            <input class="form-control" placeholder="__placeholder__" value="__value__">
        </div>
    </div>
</x-core::custom-template>

<x-core::custom-template id="rv_media_crop_image">
    <img src="__src__" style="display: block;max-width: 100%">
</x-core::custom-template>

<div class="media-download-popup" style="display: none">
    <x-core::alert type="success">{{ trans('core/media::media.prepare_file_to_download') }}</x-core::alert>
</div>
