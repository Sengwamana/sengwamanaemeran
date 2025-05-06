<div class="d-flex">
<x-core::form.checkbox label="{{ trans('core/acl::permissions.all') }}" id="allTreeChecked" margin-zero class="label label-default allTree">
    <x-slot:label>
        <x-core::badge lite color="primary" :label="trans('core/acl::permissions.all')" />
    </x-slot:label>
</x-core::form.checkbox>
<div id="sidetreecontrol" class="ms-3"><a href="?#">{{ trans('core/base::tree-category.collapse_all') }}</a> | <a href="?#">{{ trans('core/base::tree-category.expand_all') }}</a></div>
</div>
