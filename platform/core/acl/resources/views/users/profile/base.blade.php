@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="user-profile">
        <x-core::card>
            <x-core::card.header>
                <x-core::tab class="card-header-tabs">
                    @if ($canChangeProfile)
                        <x-core::tab.item
                            :is-active="true"
                            id="profile"
                            icon="ti ti-user"
                            label="{{ trans('core/acl::users.info.title') }}"
                        />

                        <x-core::tab.item
                            id="avatar"
                            icon="ti ti-camera-selfie"
                            label="{{ trans('core/acl::users.avatar') }}"
                        />

                        <x-core::tab.item
                            id="change-password"
                            icon="ti ti-lock"
                            label="{{ trans('core/acl::users.change_password') }}"
                        />

                        <x-core::tab.item
                            id="preferences"
                            icon="ti ti-settings"
                            label="{{ trans('core/acl::users.preferences') }}"
                        />
                    @endif

                    {!! apply_filters(ACL_FILTER_PROFILE_FORM_TABS, null) !!}
                </x-core::tab>
            </x-core::card.header>

            <x-core::card.body>
                <x-core::tab.content>
                    @if ($canChangeProfile)
                        <x-core::tab.pane
                            id="profile"
                            :is-active="true"
                        >
                            {!! $form !!}
                        </x-core::tab.pane>

                        <x-core::tab.pane id="avatar">
                            <x-core::crop-image
                                :label="trans('core/acl::users.avatar')"
                                name="avatar_file"
                                :value="$user->avatar_url"
                                :action="route('users.profile.image', $user->id)"
                                :delete-action="route('users.profile.image.destroy', $user->getKey())"
                                :can-delete="$user->avatar_id"
                                rounded="pill"
                                :hidden-copper="!$canChangeProfile"
                                style="--bb-avatar-size: 10rem;"
                            />
                        </x-core::tab.pane>

                        <x-core::tab.pane id="change-password">
                            {!! $passwordForm !!}
                        </x-core::tab.pane>

                        <x-core::tab.pane id="preferences">
                            {!! $preferenceForm !!}
                        </x-core::tab.pane>
                    @endif

                    {!! apply_filters(ACL_FILTER_PROFILE_FORM_TAB_CONTENTS, null) !!}
                </x-core::tab.content>
            </x-core::card.body>
        </x-core::card>
    </div>
@endsection

@if ($canChangeProfile)
    @push('footer')
        {!! JsValidator::formRequest(Botble\ACL\Http\Requests\UpdateProfileRequest::class, '#profile-form') !!}
        {!! JsValidator::formRequest(Botble\ACL\Http\Requests\UpdatePasswordRequest::class, '#password-form') !!}
    @endpush
@endif
