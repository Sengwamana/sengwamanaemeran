@if (!empty($menu) && $menu->id)
    <div class="core-menu-structure">
        <input
            type="hidden"
            name="deleted_nodes"
        >
        <textarea
            name="menu_nodes"
            id="nestable-output"
            class="d-none"
        ></textarea>

        <div class="row row-cards">
            <div class="col-md-4">
                @php
                    do_action(MENU_ACTION_SIDEBAR_OPTIONS);
                @endphp

                <x-core::card>
                    <x-core::card.header>
                        <a
                            class="d-flex justify-content-between w-100 align-items-center text-decoration-none"
                            data-bs-toggle="collapse"
                            data-parent="#accordion"
                            href="#collapseCustomLink"
                        >
                            <x-core::card.title>
                                {{ trans('packages/menu::menu.add_link') }}
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
                        id="collapseCustomLink"
                        class="box-links-for-menu collapse"
                    >
                        <x-core::card.body>
                            <div
                                id="external_link"
                                class="the-box"
                            >
                                <div
                                    class="node-content"
                                    id="menu-node-create-form"
                                >
                                    {!! Botble\Menu\Forms\MenuNodeForm::create()->renderForm([], false, true, false) !!}
                                </div>
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
            </div>
            <div class="col-md-8">
                <x-core::card class="mb-3">
                    <x-core::card.header>
                        <x-core::card.title>{{ trans('packages/menu::menu.structure') }}</x-core::card.title>
                    </x-core::card.header>
                    <x-core::card.body>
                        <div
                            class="dd nestable-menu"
                            id="nestable"
                            data-depth="0"
                        >
                            {!! Menu::generateMenu([
                                'slug' => $menu->slug,
                                'view' => 'packages/menu::partials.menu',
                                'theme' => false,
                                'active' => false,
                            ]) !!}
                        </div>
                    </x-core::card.body>
                </x-core::card>

                @if (defined('THEME_MODULE_SCREEN_NAME'))
                    <x-core::card>
                        <x-core::card.header>
                            <x-core::card.title>{{ trans('packages/menu::menu.menu_settings') }}</x-core::card.title>
                        </x-core::card.header>
                        <x-core::card.body>
                            <div class="row">
                                <div class="col-md-4">
                                    <p><i>{{ trans('packages/menu::menu.display_location') }}</i></p>
                                </div>
                                <div class="col-md-8">
                                    @foreach (Menu::getMenuLocations() as $location => $description)
                                        <div @class(['mb-3' => ! $loop->last])>
                                            <x-core::form.checkbox
                                                :label="$description"
                                                id="menu_location_{{ $location }}"
                                                name="locations[]"
                                                :checked="in_array($location, $locations)"
                                                :value="$location"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </x-core::card.body>
                    </x-core::card>
                @endif
            </div>
        </div>
    </div>
@endif
