<x-core::card class="mb-4">
    <x-core::card.header>
        <div>
            <x-core::card.title>
                {{ trans($data['name']) }}
            </x-core::card.title>
        </div>
    </x-core::card.header>
    <div class="table-responsive">
        <x-core::table
            :hover="false"
            :striped="false"
        >
            <x-core::table.header>
                <x-core::table.header.cell class="w-25">
                    {{ trans('core/setting::setting.template') }}
                </x-core::table.header.cell>

                <x-core::table.header.cell>
                    {{ trans('core/setting::setting.description') }}
                </x-core::table.header.cell>

                <x-core::table.header.cell class="text-end">
                    {{ trans('core/base::tables.operations') }}
                </x-core::table.header.cell>
            </x-core::table.header>

            @foreach ($data['templates'] as $key => $template)
                <x-core::table.body.row>
                    <x-core::table.body.cell class="template-name w-25">
                        @php
                            $isOn = (bool) (get_setting_email_status($type, $module, $key));
                        @endphp

                        <span
                            @class(['text-muted text-decoration-line-through' => !$isOn])
                            @if (!$isOn)
                                data-bs-toggle="tooltip"
                                title="{{ trans('core/setting::setting.email.template_off_status_helper') }}"
                            @endif
                        >
                            {{ trans($template['title']) }}
                        </span>
                    </x-core::table.body.cell>

                    <x-core::table.body.cell>
                        {{ trans($template['description']) }}
                    </x-core::table.body.cell>

                    @if (Arr::get($template, 'can_off', false))
                        <x-core::table.body.cell
                            class="text-end"
                            data-bs-toggle="tooltip"
                            :title="$isOn ? trans('core/setting::setting.email.template_turn_off') : trans('core/setting::setting.email.template_turn_on')"
                        >
                            <x-core::form.toggle
                                :name="get_setting_email_status_key($type, $module, $key)"
                                :checked="$isOn"
                                data-bb-toggle="email-status-toggle"
                                :data-change-url="route('settings.email.template.status.update', [$type, $module, $key])"
                                wrapper-class="mb-0 d-inline-block"
                            />
                        </x-core::table.body.cell>
                    @else
                        <x-core::table.body.cell class="text-end" data-bs-toggle="tooltip" :title="trans('core/setting::setting.general.disabled_helper')">
                            <x-core::form.toggle
                                :name="get_setting_email_status_key($type, $module, $key)"
                                :checked="1"
                                :disabled="true"
                                :readonly="true"
                                wrapper-class="mb-0 d-inline-block"
                            />
                        </x-core::table.body.cell>
                    @endif
                </x-core::table.body.row>
            @endforeach
        </x-core::table>
    </div>
</x-core::card>
