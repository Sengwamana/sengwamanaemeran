@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @php
        do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), WIDGET_MANAGER_MODULE_SCREEN_NAME);
    @endphp
    <div
        class="widget-main"
        id="wrap-widgets"
    >
        <x-core::alert type="info">
            {{ trans('packages/widget::widget.usage_instruction') }}
        </x-core::alert>

        <div class="row row-cols-1 row-cols-md-2">
            <div class="col">
                <h2>{{ trans('packages/widget::widget.available') }}</h2>
                <div
                    id="wrap-widget-1"
                    class="row row-cols-1 row-cols-md-2"
                >
                    @foreach (Widget::getWidgets() as $widget)
                        <li
                            data-id="{{ $widget->getId() }}"
                            class="col mb-3 widget-item"
                        >
                            <form method="post">
                                <input
                                    name="id"
                                    type="hidden"
                                    value="{{ $widget->getId() }}"
                                >
                                <x-core::card>
                                    <x-core::card.header class="d-flex justify-content-between p-3">
                                        {{ $widget->getConfig()['name'] }}

                                        <x-core::card.header.button class="d-none">
                                            <x-core::icon
                                                size="sm"
                                                name="ti ti-chevron-down"
                                            />
                                        </x-core::card.header.button>
                                    </x-core::card.header>
                                </x-core::card>
                                <div class="widget-description mt-1">
                                    <x-core::form.helper-text>
                                        {{ $widget->getConfig()['description'] }}
                                    </x-core::form.helper-text>
                                </div>
                            </form>
                        </li>
                    @endforeach
                </div>
            </div>
            <div
                class="col"
                id="added-widget"
            >
                {!! apply_filters(WIDGET_TOP_META_BOXES, null, WIDGET_MANAGER_MODULE_SCREEN_NAME) !!}
                <div class="row row-cols-1 row-cols-xxl-2 gy-2 mt-3">
                    @foreach (WidgetGroup::getGroups() as $group)
                        <div
                            class="col sidebar-item"
                            data-id="{{ $group->getId() }}"
                        >
                            <x-core::card>
                                <x-core::card.header class="d-flex justify-content-between">
                                    <div>
                                        <x-core::card.title>{{ $group->getName() }}</x-core::card.title>
                                        <x-core::card.subtitle class="mb-1">{{ $group->getDescription() }}</x-core::card.subtitle>
                                    </div>
                                    <x-core::card.header.button class="button-sidebar">
                                        <x-core::icon
                                            size="sm"
                                            name="ti ti-chevron-up"
                                        />
                                    </x-core::card.header.button>
                                </x-core::card.header>

                                <x-core::card.body>
                                    <ul
                                        id="wrap-widget-{{ $loop->index + 2 }}"
                                        class="p-1"
                                    >
                                        @include('packages/widget::item', [
                                            'widgetAreas' => $group->getWidgets(),
                                        ])
                                    </ul>
                                </x-core::card.body>
                            </x-core::card>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer')
    <script>
        'use strict';
        var BWidget = BWidget || {};
        BWidget.routes = {
            'delete': '{{ route('widgets.destroy', ['ref_lang' => BaseHelper::stringify(request()->input('ref_lang'))]) }}',
            'save_widgets_sidebar': '{{ route('widgets.save_widgets_sidebar', ['ref_lang' => BaseHelper::stringify(request()->input('ref_lang'))]) }}'
        };
    </script>
@endpush
