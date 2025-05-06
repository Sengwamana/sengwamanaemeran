<h3>{{ $config['name'] }}</h3>

<ul>
    @foreach ($items as $item)
        <li>
            <a
                href="{{ url((string) $item->url) }}"
                title="{{ $item->label }}"
                {!! $item->attributes ? BaseHelper::clean($item->attributes) : null !!}
                @if ($item->is_open_new_tab)
                    target="_blank"
                @endif
            >{{ $item->label }}</a>
        </li>
    @endforeach
</ul>
