@extends('core/table::table')

@push('footer')
    <x-core::modal.action
        id="modal-confirm-delete-records"
        type="danger"
        :title="trans('plugins/request-log::request-log.empty_logs')"
        :description="trans('plugins/request-log::request-log.confirm_empty_logs_msg')"
        :submit-button-label="trans('core/base::tables.delete')"
        :submit-button-attrs="['class' => 'button-delete-records', 'data-url' => route('request-log.empty')]"
        :close-button-label="trans('core/base::tables.cancel')"
    />
@endpush
