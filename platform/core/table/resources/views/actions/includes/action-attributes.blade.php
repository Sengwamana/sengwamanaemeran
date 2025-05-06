@if($action->getType() === 'a')
    href="{{ $action->hasUrl() ? $action->getUrl() : 'javascript:void(0);' }}"
@elseif($action->hasUrl())
    type="{{ $action->getType() }}"
    data-url="{{ $action->getUrl() }}"
@endif

@if($bsToggle = $action->getAttribute('data-bs-toggle'))
    data-bs-toggle="{{ $bsToggle }}"
@endif
@if($bsTarget = $action->getAttribute('data-bs-target'))
    data-bs-target="{{ $bsTarget }}"
@endif

@if ($action->isAction())
    data-dt-single-action
    data-method="{{ $action->getActionMethod() }}"
    @if ($action->isConfirmation())
        data-confirmation-modal="{{ $action->isConfirmation() ? 'true' : 'false' }}"
        data-confirmation-modal-title="{{ $action->getConfirmationModalTitle() }}"
        data-confirmation-modal-message="{{ $action->getConfirmationModalMessage() }}"
        data-confirmation-modal-button="{{ $action->getConfirmationModalButton() }}"
        data-confirmation-modal-cancel-button="{{ $action->getConfirmationModalCancelButton() }}"
    @endif
@elseif($action->shouldOpenUrlInNewTable())
    target="_blank"
@endif

{!! Html::attributes($action->getAttributes()) !!}
