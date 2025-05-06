@if ($options)
    @php($id = Str::slug($name) . '-' . time())

    <x-core::card class="mb-3">
        <x-core::card.header>
            <a
                class="d-flex justify-content-between w-100 align-items-center text-decoration-none"
                data-bs-toggle="collapse"
                data-parent="#accordion"
                href="#{{ $id }}"
                aria-expanded="{{ Str::slug($name) === 'pages' ? 'true' : false }}"
            >
                <x-core::card.title>
                    {{ $name }}
                </x-core::card.title>

                <button
                    type="button"
                    class="btn-action"
                >
                    <x-core::icon name="ti ti-chevron-down" size="sm" />
                </button>
            </a>
        </x-core::card.header>
        <div
            id="{{ $id }}"
            @class([
                'box-links-for-menu panel-collapse collapse',
                'show' => Str::slug($name) === 'pages',
            ])
        >
            <x-core::card.body
                class="overflow-auto"
                style="max-height: 20rem;"
            >
                <div class="the-box">
                    {!! $options !!}
                </div>
            </x-core::card.body>

            <x-core::card.footer class="text-end">
                <x-core::button
                    type="button"
                    class="btn-add-to-menu"
                    :data-url="route('menus.get-node')"
                    icon="ti ti-plus"
                >
                    {{ trans('packages/menu::menu.add_to_menu') }}
                </x-core::button>
            </x-core::card.footer>
        </div>
    </x-core::card>
@endif
