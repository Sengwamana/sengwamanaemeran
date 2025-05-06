@extends('packages/installer::layouts.master')

@section(
    'pageTitle',
     trans(
         'packages/installer::installer.install_step_title',
         ['step' => 3, 'title' => trans('packages/installer::installer.environment.wizard.title')]
     )
)

@section('header')
    <x-core::card.title>
        {{ trans('packages/installer::installer.environment.wizard.title') }}
    </x-core::card.title>
@endsection

@section('content')
    <form
        id="environment-form"
        method="post"
        action="{{ route('installers.environments.createenvironments.store') }}"
    >
        @csrf
        <div class="row">
            <x-core::form.text-input
                wrapper-class="col-md-6"
                id="app_name"
                name="app_name"
                :value="old('app_name', config('app.name'))"
                :label="trans('packages/installer::installer.environment.wizard.form.app_name_label')"
            />

            <x-core::form.text-input
                wrapper-class="col-md-6"
                id="app_url"
                name="app_url"
                type="text"
                :value="old('app_url', url(''))"
                :label="trans('packages/installer::installer.environment.wizard.form.app_url_label')"
            />

            <x-core::form.select
                id="database_connection"
                name="database_connection"
                :label="trans('packages/installer::installer.environment.wizard.form.db_connection_label')"
                :options="[
                    'mysql' => trans('packages/installer::installer.environment.wizard.form.db_connection_label_mysql'),
                ]"
            />

            <x-core::form.text-input
                id="database_hostname"
                name="database_hostname"
                type="text"
                :label="trans('packages/installer::installer.environment.wizard.form.db_host_label')"
                value="{{ old('database_hostname', DB::connection('mysql')->getConfig()['host']) }}"
                :helper-text="trans('packages/installer::installer.environment.wizard.form.db_host_helper')"
            />

            <x-core::form.text-input
                wrapper-class="col-md-6"
                id="database_port"
                name="database_port"
                type="text"
                :label="trans('packages/installer::installer.environment.wizard.form.db_port_label')"
                :value="old('database_port', DB::connection('mysql')->getConfig()['port'])"
            />

            <x-core::form.text-input
                wrapper-class="col-md-6"
                id="database_name"
                name="database_name"
                :value="old('database_name')"
                :label="trans('packages/installer::installer.environment.wizard.form.db_name_label')"
                :placeholder="trans('packages/installer::installer.environment.wizard.form.db_name_placeholder')"
            />

            <x-core::form.text-input
                wrapper-class="col-md-6"
                id="database_username"
                name="database_username"
                :value="old('database_username')"
                :label="trans('packages/installer::installer.environment.wizard.form.db_username_label')"
                :placeholder="trans('packages/installer::installer.environment.wizard.form.db_username_placeholder')"
            />

            <x-core::form.text-input
                wrapper-class="col-md-6"
                id="database_password"
                name="database_password"
                :placeholder="trans('packages/installer::installer.environment.wizard.form.db_password_placeholder')"
                :value="old('database_password')"
                :label="trans('packages/installer::installer.environment.wizard.form.db_password_label')"
            />

        </div>
    </form>
@endsection

@section('footer')
    <x-core::button
        color="primary"
        type="submit"
        form="environment-form"
    >
        {{ trans('packages/installer::installer.environment.wizard.form.buttons.install') }}
    </x-core::button>
@endsection
