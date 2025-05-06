<x-core::button tag="a" :href="route('roles.duplicate', $role->getKey())" icon="ti ti-copy">
    {{ trans('core/acl::permissions.duplicate') }}
</x-core::button>
