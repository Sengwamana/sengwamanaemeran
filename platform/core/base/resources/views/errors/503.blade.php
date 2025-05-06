@php
    PageTitle::setTitle(__('503 Service Unavailable'));
@endphp

@extends('core/base::errors.master')

@section('content')
    <div class="empty">
        <div class="empty-img">
            <img
                src="{{ asset('vendor/core/core/base/images/503.svg') }}"
                alt="503"
                height="128"
            >
        </div>
        <p class="empty-title">{{ __('Temporarily down for maintenance') }}</p>
        <p class="empty-subtitle text-secondary">
            {!! BaseHelper::clean(
                __(
                    "If you are the administrator and you can't access your site after enabling maintenance mode, just need to delete file <strong>storage/framework/down</strong> to turn-off maintenance mode.",
                ),
            ) !!}
        </p>
    </div>
@endsection
