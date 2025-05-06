@if (is_array($item->dependencies))
    <ul>
        @foreach ($item->dependencies as $dependencyName => $dependencyVersion)
            <li class="py-1">{{ $dependencyName }}: <x-core::badge
                    color="primary"
                    :label="$dependencyVersion"
                /></li>
        @endforeach
    </ul>
@else
    <p class="ms-3">&mdash;</p>
@endif
