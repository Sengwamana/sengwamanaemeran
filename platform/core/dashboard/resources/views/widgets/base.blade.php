@if (empty($widgetSetting) || $widgetSetting->status == 1)
    <div
        @class(['widget-item col-12 d-flex', $widget->column])
        id="{{ $widget->name }}"
        data-url="{{ $widget->route }}"
    >
        <x-core::card size="sm" @class([
            'flex-fill',
            'widget-load-has-callback' => $widget->hasLoadCallback,
        ])>
            <x-core::card.header>
                <x-core::card.title>
                    {{ $widget->title }}
                </x-core::card.title>

                <x-core::card.actions class="btn-actions">
                    @if (Arr::get($settings, 'show_predefined_ranges', false) && count($predefinedRanges))
                        <x-core::dropdown wrapper-class="d-flex align-items-center me-2 predefined_range">
                            <x-slot:trigger>
                                <a
                                    class="dropdown-toggle text-muted"
                                    href="#"
                                    data-bs-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                >
                                    {{ ($selectedRange = Arr::get($settings, 'predefined_range')) ? Arr::get(Arr::first($predefinedRanges, fn($item) => $item['key'] === $selectedRange), 'label') : Arr::get(Arr::first($predefinedRanges), 'label') }}
                                </a>
                            </x-slot:trigger>

                            @foreach ($predefinedRanges as $range)
                                <x-core::dropdown.item
                                    :label="$range['label']"
                                    :active="$selectedRange === $range['key']"
                                    data-key="{{ $range['key'] }}"
                                    data-label="{{ $range['label'] }}"
                                />
                            @endforeach
                        </x-core::dropdown>
                    @endif
                </x-core::card.actions>
            </x-core::card.header>
            <div
                @class([
                    'd-flex flex-column justify-content-between h-100 widget-content',
                    $widget->bodyClass,
                    Arr::get($settings, 'state'),
                ])
                style="min-height: 10rem;"
            ></div>
        </x-core::card>
    </div>
@endif
