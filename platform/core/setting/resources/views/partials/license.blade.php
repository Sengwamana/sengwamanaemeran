@php
    $manageLicense = auth()
        ->user()
        ->hasPermission('core.manage.license');

    $licenseTitle = __('License');
    $licenseDescription = __('Setup license code');
@endphp

<v-license-form
    id="license-form"
    verify-url="{{ route('settings.license.verify.index') }}"
    activate-license-url="{{ route('settings.license.activate') }}"
    deactivate-license-url="{{ route('settings.license.deactivate') }}"
    reset-license-url="{{ route('settings.license.reset') }}"
    v-slot="{ initialized, loading, verified, license, deactivateLicense, resetLicense }"
    v-cloak
>
    <template v-if="initialized && (! verified || ! license)">
        <x-core-setting::section
            :title="$licenseTitle"
            :description="$licenseDescription"
        >
            <x-core::license.form />

            <x-core::loading v-if="loading" />
        </x-core-setting::section>
    </template>

    <template v-if="initialized && verified && license">
        @if(! config('core.base.general.hide_activated_license_info', false))
            <x-core-setting::section
                :title="$licenseTitle"
                :description="$licenseDescription"
            >
                <p class="text-info">
                    <span v-if="license.licensed_to">Licensed to <span v-text="license.licensed_to"></span>.
                    </span>Activated
                        since <span v-text="license.activated_at"></span>.
                </p>

                <div>
                    <x-core::button
                        color="warning"
                        @click="deactivateLicense"
                        :disabled="!$manageLicense"
                    >
                        Deactivate license
                    </x-core::button>
                </div>
            </x-core-setting::section>
        @endif
    </template>
</v-license-form>
