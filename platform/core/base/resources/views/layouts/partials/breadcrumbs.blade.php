<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        @foreach ($breadcrumbs as $breadcrumb)
            <li
                @class(['breadcrumb-item', 'active' => $loop->last])
                @if ($loop->last) aria-current="page" @endif
            >
            @if ($breadcrumb->url && !$loop->last)
                <a class="mb-0 d-inline-block fs-6 lh-1" href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
            @else
                <h1 class="mb-0 d-inline-block fs-6 lh-1">{{ Str::limit($breadcrumb->title, 60) }}</h1>
            @endif
        </li>
        @endforeach
    </ol>
</nav>
