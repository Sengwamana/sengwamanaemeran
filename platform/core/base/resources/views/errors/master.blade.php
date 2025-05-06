@php
    Assets::removeStyles(['fontawesome', 'select2', 'toastr', 'datepicker', 'spectrum'])
        ->removeScripts(['spectrum', 'jquery-waypoints', 'stickytableheaders', 'toastr', 'core', 'cookie', 'select2', 'datepicker', 'modernizr', 'ie8-fix', 'excanvas']);
@endphp

<x-core::layouts.base body-class="border-top-wide border-primary d-flex flex-column">
    <x-slot:title>
        @yield('title')
    </x-slot:title>

    <div class="page page-center">
        <div class="container py-4 container-tight">
            @yield('content')
        </div>
    </div>
</x-core::layouts.base>
