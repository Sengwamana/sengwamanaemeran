@extends('packages/theme::errors.master')

@section('title', __('500 Internal Server Error'))

@section('content')
    <div class="empty">
        <div class="empty-header">500</div>
        <p class="empty-title">{{ __('Internal Server Error') }}</p>
        <p class="empty-subtitle text-secondary">
            {{ __('Something is broken. Please let us know what you were doing when this error occurred. We will fix it as soon as possible. Sorry for any inconvenience caused.') }}
        </p>

        <p class="empty-subtitle text-secondary">{!! BaseHelper::clean(__('Please try again in a few minutes, or alternatively return to the homepage by <a href=":link">clicking here</a>.', ['link' => BaseHelper::getHomepageUrl()])) !!}</p>

        <div class="empty-action">
            <x-core::button
                tag="a"
                href="{{ BaseHelper::getHomepageUrl() }}"
                color="primary"
                icon="ti ti-arrow-left"
            >
                {{ __('Take me home') }}
            </x-core::button>
        </div>
    </div>
@endsection
