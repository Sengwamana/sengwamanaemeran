@php
    /** @var Botble\Table\Actions\Action $action */
@endphp

<li>
    <a
        @if (!$action->getAttribute('class'))
            @class([
                'dropdown-item',
                str_replace('btn-', 'text-', $action->getColor()),
            ])
        @endif
        @include('core/table::actions.includes.action-attributes') >
        @include('core/table::actions.includes.action-icon')

        <span class="ms-1">{{ $action->getLabel() }}</span>
    </a>
</li>
