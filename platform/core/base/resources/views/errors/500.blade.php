@php
    PageTitle::setTitle(__('500 Internal Server Error'));
@endphp

@extends('core/base::errors.master')

@section('content')
    <div class="empty">
        <div class="empty-header">500</div>
        <p class="empty-title">{{ __('Internal Server Error') }}</p>
        <p class="empty-subtitle text-secondary">
            {{ __('Something is broken. Please let us know what you were doing when this error occurred. We will fix it as soon as possible. Sorry for any inconvenience caused.') }}
        </p>
        <div class="empty-action">
            <x-core::button
                tag="a"
                href="{{ route('dashboard.index') }}"
                color="primary"
                icon="ti ti-arrow-left"
            >
                {{ __('Take me home') }}
            </x-core::button>
        </div>
    </div>
@endsection
