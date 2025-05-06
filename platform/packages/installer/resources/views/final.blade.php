@extends('packages/installer::layouts.master')

@section('pageTitle', trans('packages/installer::installer.final.pageTitle'))

@section('content')
    <div class="d-flex flex-column justify-content-center text-center h-100">
        <x-core::icon size="lg" name="ti ti-circle-check" class="d-block mx-auto text-success mb-2" />

        <h3 class="fw-bold mb-2">{{ trans('packages/installer::installer.final.pageTitle') }}</h3>

        <p class="text-secondary">{{ trans('packages/installer::installer.final.message') }}</p>
    </div>
@endsection

@section('footer')
    <x-core::button
        tag="a"
        color="primary"
        :href="route('access.login')"
    >
        {{ trans('packages/installer::installer.final.exit') }}
    </x-core::button>
@endsection
