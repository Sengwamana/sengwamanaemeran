@extends('core/table::table')

@push('footer')
    <x-core::modal.action
        id="modal-confirm-delete-records"
        type="danger"
        :title="trans('plugins/audit-log::history.empty_logs')"
        :description="trans('plugins/audit-log::history.confirm_empty_logs_msg')"
        :submit-button-label="trans('core/base::tables.delete')"
        :submit-button-attrs="['class' => 'button-delete-records', 'data-url' => route('audit-log.empty')]"
        :close-button-label="trans('core/base::tables.cancel')"
    />
@endpush
