@if (count($widgetAreas))
    @foreach ($widgetAreas as $item)
        @continue(! class_exists($item->widget_id, false))

        @php
            $widget = new $item->widget_id();
        @endphp

        <li
            data-id="{{ $widget->getId() }}"
            data-position="{{ $item->position }}"
            class="mb-3 widget-item"
        >
            <x-core::card>
                <x-core::card.header class="d-flex py-1 px-3 justify-content-between">
                    {{ $widget->getConfig()['name'] }}

                    <x-core::card.header.button>
                        <x-core::icon
                            size="sm"
                            name="ti ti-chevron-down"
                        />
                    </x-core::card.header.button>
                </x-core::card.header>
            </x-core::card>

            <x-core::form.fieldset class="widget-content">
                <form method="post">
                    <input
                        name="id"
                        type="hidden"
                        value="{{ $widget->getId() }}"
                    >
                    {!! $widget->form($item->sidebar_id, $item->position) !!}
                    <div class="widget-control-actions mt-3 d-flex justify-content-between">
                        <x-core::button
                            type="button"
                            :outlined="true"
                            class="widget-control-delete"
                        >
                            {{ trans('packages/widget::widget.delete') }}
                        </x-core::button>

                        <x-core::button
                            type="button"
                            color="primary"
                            class="widget-save"
                        >
                            {{ trans('core/base::forms.save_and_continue') }}
                        </x-core::button>
                    </div>
                </form>
            </x-core::form.fieldset>
        </li>
    @endforeach
@else
    <li class="dropzone px-1 py-3 text-center">
        <div class="dz-default dz-message">{{ trans('packages/widget::widget.drag_widget_to_sidebar') }}</div>
    </li>
@endif
