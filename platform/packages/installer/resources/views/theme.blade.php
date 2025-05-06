@extends('packages/installer::layouts.master')

@section(
    'pageTitle',
     trans(
         'packages/installer::installer.install_step_title',
         ['step' => 4, 'title' => trans('packages/installer::installer.theme.title')]
     )
)

@section('header')
    <div>
        <x-core::card.title class="text-start">
            {{ trans('packages/installer::installer.theme.title') }}
        </x-core::card.title>

        <x-core::card.subtitle>
            {{ trans('packages/installer::installer.theme.message') }}
        </x-core::card.subtitle>
    </div>
@endsection

@section('content')
    <x-core::form :url="route('installers.themes.store')" method="post" id="choose-theme-form">
        <x-core::form.image-check name="theme" :options="$themes" :value="old('theme', array_key_first($themes))" />
    </x-core::form>
@endsection

@section('footer')
    <x-core::button type="submit" color="primary" form="choose-theme-form">
        {{ trans('packages/installer::installer.next') }}
    </x-core::button>
@endsection
