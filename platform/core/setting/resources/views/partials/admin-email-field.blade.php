<x-core-setting::form-group
    data-bb-toggle="admin-email"
    data-emails="{{ Js::encode(get_admin_email()) }}"
    data-max="4"
>
    <x-core::form.label for="admin_email">
        {{ trans('core/setting::setting.general.admin_email') }}

        <x-slot:description>
            <a
                id="add"
                class="link btn-link cursor-pointer"
                href="#"
                data-placeholder="{{ sprintf('email@%s', request()->getHost()) }}"
            >
                <small>+ {{ trans('core/setting::setting.email_add_more') }}</small>
            </a>
        </x-slot:description>
    </x-core::form.label>

    <x-core::form.helper-text class="mt-2">
        {{ trans('core/setting::setting.emails_warning', ['count' => 4]) }}
    </x-core::form.helper-text>
</x-core-setting::form-group>
