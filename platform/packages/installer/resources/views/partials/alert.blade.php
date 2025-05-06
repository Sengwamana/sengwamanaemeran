@if (session('message'))
    <x-core::alert
        type="success"
    >
        @if (is_array(session('message')))
            {{ session('message')['message'] }}
        @else
            {{ session('message') }}
        @endif
    </x-core::alert>
@endif
@if (session()->has('errors'))
    <x-core::alert
        :title="trans('packages/installer::installer.forms.errorTitle')"
        type="danger"
    >
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </x-core::alert>
@endif
