<!DOCTYPE html>
<html lang="{{ Str::replace('_', '-', App::getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta
        http-equiv="X-UA-Compatible"
        content="IE=edge"
    >
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <title>@yield('pageTitle', trans('packages/installer::installer.title'))</title>

    <meta
        name="description"
        content="Installation Botble CMS v{{ get_cms_version() }}"
    >

    <link
        href="{{ asset('vendor/core/core/base/images/favicon.png') }}"
        rel="icon"
    >

    @include('core/base::components.layouts.header')

    <link
        href="{{ asset('vendor/core/packages/installer/css/style.css') }}?v={{ get_cms_version() }}"
        rel="stylesheet"
    />

    <style>
        [v-cloak],
        [x-cloak] {
            display: none;
        }
    </style>

    @php
        Assets::getFacadeRoot()
            ->removeStyles([
                'fontawesome',
                'select2',
                'custom-scrollbar',
                'datepicker',
                'spectrum',
                'fancybox',
            ])
            ->removeScripts([
                'excanvas',
                'ie8-fix',
                'modernizr',
                'select2',
                'datepicker',
                'cookie',
                'toastr',
                'custom-scrollbar',
                'stickytableheaders',
                'jquery-waypoints',
                'spectrum',
                'fancybox',
                'fslightbox',
            ]);
    @endphp
    {!!  Assets::renderHeader(['core']) !!}

    <link
        href="{{ BaseHelper::getGoogleFontsURL() }}"
        rel="preconnect"
    >
    <link
        href="{{ BaseHelper::getGoogleFontsURL('css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap') }}"
        rel="stylesheet"
    >

    @yield('styles')
</head>

<body>
    @php
        use Botble\Installer\InstallerStep\InstallerStep;

        $currentStep = InstallerStep::currentStep();
    @endphp

    <div class="page-wrapper justify-content-center min-h-full">
        <div class="page-body page-content mt-8 mb-8">
            <div class="container-xl installer-container">
                <div class="row mb-6">
                    <div class="col">
                        <h3 class="h1 page-title justify-content-center text-white">
                            {{ trans('packages/installer::installer.title') }}
                        </h3>
                    </div>
                </div>

                <div class="row installer-wrapper">
                    <div class="col-md-3 p-4">
                        <div class="steps-backdrop"></div>
                        <x-core::step :counter="true" :vertical="true">
                            @foreach(InstallerStep::getItems() as $step)
                                <x-core::step.item :is-active="$currentStep === $loop->iteration">
                                    @if($step->getRoute() && $currentStep > $loop->iteration)
                                        <a href="{{ route($step->getRoute()) }}">{{ $step->getLabel() }}</a>
                                    @else
                                        {{ $step->getLabel() }}
                                    @endif
                                </x-core::step.item>
                            @endforeach
                        </x-core::step>
                    </div>
                    <div class="col-md-9 p-0">
                        <x-core::card class="h-100">
                            @hasSection('header')
                                <x-core::card.header>
                                    @yield('header')
                                </x-core::card.header>
                            @endif

                            <x-core::card.body>
                                @include('packages/installer::partials.alert')

                                @yield('content')
                            </x-core::card.body>

                            @hasSection('footer')
                                <x-core::card.footer class="d-flex justify-content-end">
                                    @yield('footer')
                                </x-core::card.footer>
                            @endif
                        </x-core::card>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!!  Assets::renderFooter() !!}

    <script type="text/javascript">
        var BotbleVariables = BotbleVariables || {
            languages: {
                notices_msg: {{ Js::from(trans('core/base::notices')) }},
            },
        };
    </script>

    @push('footer')
        @if (Session::has('success_msg') || Session::has('error_msg') || (isset($errors) && $errors->any()) || isset($error_msg))
            <script type="text/javascript">
                $(function() {
                    @if (Session::has('success_msg'))
                    Botble.showSuccess('{{ session('success_msg') }}');
                    @endif
                    @if (Session::has('error_msg'))
                    Botble.showError('{{ session('error_msg') }}');
                    @endif
                    @if (isset($error_msg))
                    Botble.showError('{{ $error_msg }}');
                    @endif
                    @if (isset($errors))
                    @foreach ($errors->all() as $error)
                    Botble.showError('{{ $error }}');
                    @endforeach
                    @endif
                })
            </script>
        @endif
    @endpush

    @yield('scripts')
</body>
</html>
