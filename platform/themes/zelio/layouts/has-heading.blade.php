@extends(Theme::getThemeNamespace('layouts.base'))

@section('content')
    <div @class(['pb-150 container', 'pt-80' => get_header_style() != 2, 'pt-160' => get_header_style() == 2])>
        <div class="mb-8 text-center">
            <h3 class="ds-3 mt-3 mb-4 text-dark">
                {!! BaseHelper::clean(Theme::get('pageTitle') ?: SeoHelper::getTitle()) !!}
            </h3>
        </div>

        {!! Theme::content() !!}
    </div>
@endsection
