@extends('packages/installer::layouts.master')

@section(
    'pageTitle',
     trans(
         'packages/installer::installer.install_step_title',
         ['step' => 6, 'title' => trans('packages/installer::installer.license.title')]
     )
)

@section('header')
    <x-core::card.title>
        {{ trans('packages/installer::installer.license.title') }}
    </x-core::card.title>
@endsection

@section('content')
    <form
        id="license-form"
        action="{{ route('installers.licenses.createlicenses.store') }}"
        method="POST"
        data-bb-toggle="activate-license"
        data-reload="true"
    >
        @csrf
        <x-core::license.form :reset="false" />
    </form>
@endsection

@section('footer')
    <div class="text-end mt-10">
        <form action="{{ route('installers.licenses.skip') }}" method="POST">
            @csrf

            <x-core::button type="submit" color="link" size="sm">
                {{ trans('packages/installer::installer.license.skip') }}
            </x-core::button>
        </form>
    </div>
@endsection
