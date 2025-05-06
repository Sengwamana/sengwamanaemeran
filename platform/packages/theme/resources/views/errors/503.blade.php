@extends('packages/theme::errors.master')

@section('title', __('503 Service Unavailable'))

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
        <p class="empty-subtitle text-secondary">{{ __('Sorry, we are doing some maintenance. Please check back soon.') }}</p>
        <p class="empty-subtitle text-secondary"><i>{!! BaseHelper::clean(__("If you are the administrator and you can't access your site after enabling maintenance mode, just need to delete file <strong>storage/framework/down</strong> to turn-off maintenance mode.")) !!}</i></p>
        @if ($email = get_admin_email()->first())
            <p class="empty-subtitle text-secondary">{!! BaseHelper::clean(__('If you need help, contact us at :mail.', ['mail' => Html::mailto($email)])) !!}</p>
        @endif
    </div>
@endsection


