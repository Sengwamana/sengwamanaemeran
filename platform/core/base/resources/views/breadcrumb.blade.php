<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        @foreach ($items as $item)
            <li
                @class(['breadcrumb-item', 'active' => $loop->last])
                @if ($loop->last) aria-current="page" @endif
            >
                @if ($item['url'] && ! $loop->last)
                    <a class="mb-0 d-inline-block fs-6 lh-1" href="{{ $item['url'] }}">{!! $item['label'] !!}</a>
                @else
                    <h1 class="mb-0 d-inline-block fs-6 lh-1">{{ Str::limit($item['label'], 60) }}</h1>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
