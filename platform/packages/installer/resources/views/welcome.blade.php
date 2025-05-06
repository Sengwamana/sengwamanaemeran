@extends('packages/installer::layouts.master')

@section(
    'pageTitle',
     trans(
         'packages/installer::installer.install_step_title',
         ['step' => 1, 'title' => trans('packages/installer::installer.welcome.title')]
     )
)

@section('header')
    <div>
        <x-core::card.title class="text-start">
            {{ trans('packages/installer::installer.welcome.title') }}
        </x-core::card.title>

        <x-core::card.subtitle>
            {{ trans('packages/installer::installer.welcome.message') }}
        </x-core::card.subtitle>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('installers.welcome.next') }}" id="welcome-form">
        @csrf

        <x-core::form.select
            :label="trans('packages/installer::installer.welcome.language')"
            name="language"
            :options="$languages"
            :value="old('language', app()->getLocale())"
        />
    </form>
@endsection

@section('footer')
    <x-core::button
        type="submit"
        color="primary"
        form="welcome-form"
    >
        {{ trans('packages/installer::installer.welcome.next') }}
    </x-core::button>
@endsection
