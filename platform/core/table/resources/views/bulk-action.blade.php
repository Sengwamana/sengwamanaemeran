@php
    /** @var Botble\Table\Actions\Action $action */
    /** @var Botble\Table\Abstracts\TableAbstract $table */
@endphp

<x-core::dropdown.item
    :label="BaseHelper::clean($action->getLabel())"
    data-trigger-bulk-action
    data-method="{{ $action->getActionMethod() }}"
    data-table-target="{{ get_class($table) }}"
    data-target="{{ get_class($action) }}"
    data-confirmation-modal-title="{{ $action->getConfirmationModalTitle() }}"
    data-confirmation-modal-message="{{ $action->getConfirmationModalMessage() }}"
    data-confirmation-modal-button="{{ $action->getConfirmationModalButton() }}"
    data-confirmation-modal-cancel-button="{{ $action->getConfirmationModalCancelButton() }}"
    href="{{ $action->getDispatchUrl() }}"
/>
