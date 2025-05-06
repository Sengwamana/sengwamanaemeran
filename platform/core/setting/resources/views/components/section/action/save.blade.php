<x-core-setting::section.action>
    <x-core::button
        type="submit"
        color="primary"
        icon="ti ti-device-floppy"
        :form="$form ?? null"
    >
        {{ trans('core/setting::setting.save_settings') }}
    </x-core::button>

    {{ $slot }}
</x-core-setting::section.action>
