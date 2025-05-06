@if ($action->hasIcon())
    @if ($action->isRenderabeIcon())
        {!! BaseHelper::clean($action->getIcon()) !!}
    @else
        @if($action->getLabel())
            <x-core::icon
                :name="$action->getIcon()"
                data-bs-toggle="tooltip"
                :data-bs-title="$action->getLabel()"
            />
        @else
            <x-core::icon :name="$action->getIcon()" />
        @endif
    @endif
@endif
