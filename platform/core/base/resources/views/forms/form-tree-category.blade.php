@extends($layout ?? BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @php
        $categories = $form->getFormOption('categories', collect());
        $canCreate = $form->getFormOption('canCreate');
        $canEdit = $form->getFormOption('canEdit');
        $canDelete = $form->getFormOption('canDelete');
        $indexRoute = $form->getFormOption('indexRoute');
        $createRoute = $form->getFormOption('createRoute');
        $editRoute = $form->getFormOption('editRoute');
        $deleteRoute = $form->getFormOption('deleteRoute');
        $updateTreeRoute = $form->getFormOption('updateTreeRoute');

        Assets::addStyles('jquery-nestable')->addScripts('jquery-nestable');
    @endphp

    <div class="row row-cards">
        <div class="col-12">
            <div class="my-2 text-end">
                @php
                    do_action(BASE_ACTION_META_BOXES, 'head', $form->getModel());
                @endphp
            </div>
        </div>

        <div class="col-md-4">
            <x-core::alert type="info">
                {{ trans('core/base::tree-category.drag_drop_info') }}
            </x-core::alert>

            <x-core::card class="tree-categories-container">
                <x-core::card.header>
                    <x-core::card.actions>
                        @if ($createRoute)
                            <x-core::button
                                tag="a"
                                type="button"
                                color="primary"
                                :href="route($createRoute)"
                                icon="ti ti-plus"
                                @class(['tree-categories-create mx-2', 'd-none' => !$canCreate])
                            >
                                {{ trans('core/base::forms.create') }}
                            </x-core::button>
                        @endif
                    </x-core::card.actions>
                </x-core::card.header>
                <x-core::card.body class="tree-categories-body">
                    <div
                        class="file-tree-wrapper"
                        data-url="{{ $indexRoute ? route($indexRoute) : '' }}"
                        @if($updateTreeRoute)
                            data-update-url="{{ route($updateTreeRoute) }}"
                        @endif
                    >
                        @include('core/base::forms.partials.tree-categories', compact('categories'))
                    </div>
                </x-core::card.body>
            </x-core::card>
        </div>

        <div class="col-md-8">
            <x-core::card class="tree-form-container">
                <x-core::card.body class="tree-form-body">
                    @include('core/base::forms.form-no-wrap')
                </x-core::card.body>
            </x-core::card>
        </div>
    </div>
@endsection

@push('footer')
    <x-core::modal.action
        type="danger"
        class="modal-confirm-delete"
        :title="trans('core/base::tree-category.delete_modal.title')"
        :description="trans('core/base::tree-category.delete_modal.message')"
        :submit-button-label="trans('core/base::tree-category.delete_button')"
        :submit-button-attrs="['data-bb-toggle' => 'modal-confirm-delete']"
    />
@endpush
