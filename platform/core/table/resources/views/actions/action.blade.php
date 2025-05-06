@php
    /** @var Botble\Table\Actions\Action $action */
@endphp

<{{ $action->getType() }}
    @include('core/table::actions.includes.action-attributes')
>
    @include('core/table::actions.includes.action-icon')

    <span @class(['sr-only' => $action->hasIcon() && $action->isIconOnly()])>{{ $action->getLabel() }}</span>
</{{ $action->getType() }}>
