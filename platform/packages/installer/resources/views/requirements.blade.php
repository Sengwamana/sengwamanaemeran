@extends('packages/installer::layouts.master')

@section(
    'pageTitle',
     trans(
         'packages/installer::installer.install_step_title',
         ['step' => 2, 'title' => trans('packages/installer::installer.requirements.title')]
     )
)

@section('header')
    <x-core::card.title>
        {{ trans('packages/installer::installer.requirements.title') }}
    </x-core::card.title>
@endsection

@section('content')
    @foreach ($requirements['requirements'] as $type => $requirement)
        <div class="list-group list-group-hoverable mb-4">
            <div class="list-group-item d-flex align-items-center justify-content-between">
                @if ($type === 'php')
                    <strong>{{ __('PHP version :version required', ['version' => $phpSupportInfo['minimum']]) }}</strong>

                    <div @class([
                        'text-success' => $phpSupportInfo['supported'],
                        'text-danger' => !$phpSupportInfo['supported'],
                    ])>
                        <span>{{ $phpSupportInfo['current'] }}</span>

                        <x-core::icon name="{{ 'ti ' . ($phpSupportInfo['supported'] ? 'ti-circle-check' : 'ti-alert-circle') }}" />
                    </div>
                @else
                    <strong>{{ ucwords($type) }}</strong>
                @endif
            </div>

            @foreach($requirements['requirements'][$type] as $extension => $enabled)
                <div class="list-group-item d-flex align-items-center justify-content-between">
                    <span>
                        {{ $type !== 'permissions' ? ucwords($extension) : $extension }}
                    </span>

                    <x-core::icon
                        :name="'ti ' . ($enabled ? 'ti-circle-check' : 'ti-alert-circle')"
                        @class([
                            'text-success' => $enabled,
                            'text-danger' => !$enabled,
                        ])
                    />
                </div>
            @endforeach
        </div>
    @endforeach
@endsection

@section('footer')
    <x-core::button
        tag="a"
        color="primary"
        :href="URL::signedRoute('installers.environments.index', [], \Carbon\Carbon::now()->addMinutes(30))"
        :disabled="isset($requirements['errors']) || !$phpSupportInfo['supported']"
    >
        {{ trans('packages/installer::installer.permissions.next') }}
    </x-core::button>
@endsection
