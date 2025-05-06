<x-core::custom-template id="{{ $repeaterId }}_template">
    @foreach($defaultFields as $defaultFieldIndex => $defaultField)
        <fieldset data-id="{{ $repeaterId }}___key__" data-index="__key__" class="form-fieldset position-relative">
            <div data-target="fields">__fields__</div>

            <x-core::button
                class="position-absolute remove-item-button"
                data-target="repeater-remove"
                data-id="{{ $repeaterId }}___key__"
                icon="ti ti-x" :icon-only="true"
                size="sm"
            />
        </fieldset>
    @endforeach
</x-core::custom-template>

<x-core::custom-template id="{{ $repeaterId }}_fields">
    {{ $defaultField }}
</x-core::custom-template>
