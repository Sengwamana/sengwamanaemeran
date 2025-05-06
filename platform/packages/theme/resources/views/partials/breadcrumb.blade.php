@if ($crumbs = Theme::breadcrumb()->getCrumbs())
    <nav
        class="d-inline-block"
        aria-label="breadcrumb"
    >
        <ol class="breadcrumb">
            @foreach ($crumbs as $i => $crumb)
                @if ($i != count($crumbs) - 1)
                    <li class="breadcrumb-item">
                        <a
                            href="{{ $crumb['url'] }}"
                            title="{{ $crumb['label'] }}"
                        >
                            {{ $crumb['label'] }}
                        </a>
                    </li>
                @else
                    <li class="breadcrumb-item active">
                        <span>
                            {{ $crumb['label'] }}
                        </span>
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif
