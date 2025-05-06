@extends('packages/theme::errors.master')

@section('title', __('404 Page Not Found'))

@section('content')
    <div class="empty">
        <div class="empty-header">404</div>
        <p class="empty-title">{{ __('Page could not be found') }}</p>
        <p class="empty-subtitle text-secondary">
            {{ __('The page you are looking for could not be found.') }}
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
