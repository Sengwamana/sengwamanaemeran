@php
    PageTitle::setTitle(__('404 Page Not Found'));
@endphp

@extends('core/base::errors.master')

@section('content')
    <div class="empty">
        <div class="empty-header">404</div>
        <p class="empty-title">{{ __('Page could not be found') }}</p>
        <p class="empty-subtitle text-secondary">
            {{ __('The page you are looking for could not be found.') }}
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
