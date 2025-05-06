@extends(Theme::getThemeNamespace('layouts.base'))

@section('content')
    <main>
        <section class="section-home-3 bg-1000 pb-130 pt-96 section-work">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 offset-lg-1">
                        {!! dynamic_sidebar('primary_sidebar') !!}
                    </div>
                    <div class="col-lg-7 pt-lg-0 pt-8">
                        {!! Theme::content() !!}
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
