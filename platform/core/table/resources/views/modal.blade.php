@php
    /** @var Botble\Table\Abstracts\TableAbstract $table */
@endphp

<x-core::modal.action
    type="danger"
    class="modal-confirm-delete"
    :title="trans('core/base::tables.confirm_delete')"
    :description="trans('core/base::tables.confirm_delete_msg')"
    :submit-button-label="trans('core/base::tables.delete')"
    :submit-button-attrs="['class' => 'delete-crud-entry']"
/>

<x-core::modal.action
    type="danger"
    class="delete-many-modal"
    :title="trans('core/base::tables.confirm_delete')"
    :description="trans('core/base::tables.confirm_delete_many_msg')"
    :submit-button-label="trans('core/base::tables.delete')"
    :submit-button-attrs="['class' => 'delete-many-entry-button']"
/>

<x-core::modal
    class="modal-bulk-change-items"
    :title="trans('core/base::tables.bulk_changes')"
    :scrollable="false"
>
    <div class="modal-bulk-change-content"></div>
    <x-slot:footer>
        <x-core::button
            color="primary"
            class="confirm-bulk-change-button"
            :data-load-url="isset($table) ? $table->getFilterInputUrl() : route('table.filter.input')"
        >
            {{ trans('core/base::tables.save') }}
        </x-core::button>

        <x-core::button data-bs-dismiss="modal">
            {{ trans('core/base::tables.cancel') }}
        </x-core::button>
    </x-slot:footer>
</x-core::modal>

<x-core::modal.action
    type="danger"
    class="bulk-action-confirm-modal"
    title="''"
    description="''"
    submit-button-label=""
    :submit-button-attrs="['class' => 'confirm-trigger-bulk-actions-button']"
/>

<x-core::modal.action
    type="danger"
    class="single-action-confirm-modal"
    title="''"
    description="''"
    submit-button-label=""
    :submit-button-attrs="['class' => 'confirm-trigger-single-action-button']"
/>
