<x-core::modal
    id="widgets-management-modal"
    data-bb-toggle="widgets-management-modal"
    :title="trans('core/dashboard::dashboard.manage_widgets')"
    :form-action="route('dashboard.hide_widgets')"
    :bodyAttrs="['class' => 'p-0']"
>
    <x-core::table>
        <x-core::table.body>
            @foreach ($widgets as $widget)
                @php
                    $widgetId = "widgets[$widget->name]";
                    $checked = !($widgetSetting = $widget->settings->first()) || $widgetSetting->status;
                @endphp

                <x-core::table.body.row>
                    <x-core::table.body.cell @class([
                        'py-0 border-0 d-flex justify-content-between align-items-center',
                        'text-decoration-line-through text-muted' => ! $checked
                    ])>
                        <label
                            for="{{ $widgetId }}"
                            class="w-full py-3 fw-bold d-block"
                        >
                            {{ $widget->title }}
                        </label>
                        <x-core::form.toggle
                            :name="$widgetId"
                            :single="true"
                            :checked="$checked"
                            data-bb-toggle="widgets-management-item"
                        />
                    </x-core::table.body.cell>
                </x-core::table.body.row>
            @endforeach
        </x-core::table.body>
    </x-core::table>
    <x-slot:footer>
        <x-core::button
            class="me-auto"
            data-bs-dismiss="modal"
        >
            {{ trans('core/base::forms.cancel') }}
        </x-core::button>

        <x-core::button
            type="submit"
            color="primary"
        >
            {{ trans('core/base::forms.save') }}
        </x-core::button>
    </x-slot:footer>
</x-core::modal>
