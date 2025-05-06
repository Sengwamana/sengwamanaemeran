<x-core::alert type="warning">
    @if ($manageLicense = auth()->guard()->user()->hasPermission('core.manage.license'))
        <div>Your license is invalid. Please activate your license!</div>
    @else
        <div>You doesn't have permission to activate the license!</div>
    @endif
</x-core::alert>

<x-core::form.text-input
    label="Your username on Envato"
    name="buyer"
    id="buyer"
    placeholder="Your Envato's username"
    :disabled="!$manageLicense"
>
    <x-slot:helper-text>
        If your profile page is <a
            href="https://codecanyon.net/user/john-smith"
            rel="nofollow"
        >https://codecanyon.net/user/john-smith</a>, then your username on Envato is
        <strong>john-smith</strong>.
    </x-slot:helper-text>
</x-core::form.text-input>

<x-core::form.text-input
    label="Purchase code"
    name="purchase_code"
    id="purchase_code"
    :disabled="!$manageLicense"
    placeholder="Ex: 10101000-0101-0100-0010-001101000010"
>
    <x-slot:helper-text>
        <a
            href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code"
            target="_blank"
        >What's this?</a>
    </x-slot:helper-text>
</x-core::form.text-input>

<x-core::form.on-off.checkbox
    name="license_rules_agreement"
    id="licenseRulesAgreement"
    :disabled="!$manageLicense"
>
    Confirm that, according to the Envato License Terms, each license entitles one person for a single
    project. Creating multiple unregistered installations is a copyright violation.
    <a
        href="https://codecanyon.net/licenses/standard"
        target="_blank"
        rel="nofollow"
    >More info</a>.
</x-core::form.on-off.checkbox>

<x-core-setting::form-group>
    <x-core::button
        type="submit"
        color="primary"
        :disabled="!$manageLicense"
    >
        Activate license
    </x-core::button>

    <div class="form-hint">
        <a
            href="{{ $licenseURL = Botble\Base\Supports\Core::make()->getLicenseUrl() }}"
            target="_blank"
            class="d-inline-block mt-2"
        > Need reset your license?
        </a> <span class="text-body">Please log in to our <a href="{{ $licenseURL }}" target="_blank">customer license manager site</a> to reset your license.</span>
    </div>

</x-core-setting::form-group>

<div>
    <p>
        A purchase code (license) is only valid for One Domain. Are you using this theme on a new domain?
        Purchase a
        <a
            href="{{ Botble\Base\Supports\Core::make()->getLicenseUrl('/buy') }}"
            target="_blank"
            rel="nofollow"
        >
            new license here
        </a>
        to get a new purchase code.
    </p>
</div>
