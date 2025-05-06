@php
    SeoHelper::setTitle($title = __('404 - Not found'));
    Theme::set('pageTitle', $title);
    Theme::fireEventGlobalAssets();
@endphp

@extends(Theme::getThemeNamespace('layouts.base'))

@section('content')
    <section class="section-404">
        <div class="container">
            <div class="row">
                <div class="text-center">
                    <img src="{{ theme_option('404_page_image') ? RvMedia::getImageUrl(theme_option('404_page_image')) :Theme::asset()->url('images/template/404.png') }}" alt="{{ Theme::getSiteTitle() }}">
                    <h1 class="mt-8">{{ __('OOPS! Something went wrong!') }}</h1>
                    <p class="fs-5 mb-4">{{ __('Locks like this page doesn’t exits. Go back to Home and continue exploring.') }}</p>
                    <a href="{{ BaseHelper::getHomepageUrl() }}" class="btn btn-gradient mt-lg-0 mt-5 ms-lg-auto">
                        {{ __('Back to Home') }}
                        <i class="ri-arrow-right-up-line"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection


