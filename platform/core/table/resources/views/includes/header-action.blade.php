<span data-action="{{ $action->getName() }}" data-href="{{ $action->getUrl() }}">
    @if($icon = $action->getIcon())
        <x-core::icon :name="$icon" />
    @endif

    {{ $action->getLabel() }}
</span>
