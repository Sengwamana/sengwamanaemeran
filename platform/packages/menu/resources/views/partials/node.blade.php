<li
    class="dd-item dd3-item @if ($row->reference_id > 0) post-item @endif"
    data-menu-item="{{ Js::encode(Arr::only(apply_filters('menu_nodes_item_data', $row)->toArray(), [
    'id',
    'menu_id',
    'parent_id',
    'reference_id',
    'reference_type',
    'url',
    'icon_font',
    'title',
    'css_class',
    'target',
    'has_child',
    'position',
])) }}"
>
    <div class="dd-handle dd3-handle"></div>
    <div class="dd3-content d-flex justify-content-between">
        <div data-update="title" class="fw-medium">{{ $row->title }}</div>
        <div class="text-end me-5">{{ Arr::last(explode('\\', (string) $row->reference_type)) ?: trans('packages/menu::menu.custom_link') }}</div>
        <a class="show-item-details" href="#">
            <x-core::icon name="ti ti-chevron-down" />
        </a>
    </div>
    <div class="item-details">
        {!! Botble\Menu\Forms\MenuNodeForm::createFromModel($row)->renderForm([], false, true, false) !!}
        <div class="text-end mt-2">
            <a
                class="btn btn-danger btn-remove btn-sm"
                href="#"
            >{{ trans('packages/menu::menu.remove') }}</a>
            <a
                class="btn btn-primary btn-cancel btn-sm"
                href="#"
            >{{ trans('packages/menu::menu.cancel') }}</a>
        </div>
    </div>
    <div class="clearfix"></div>
    @if ($row->has_child)
        {!! Menu::generateMenu([
            'menu' => $menu,
            'menu_nodes' => $row->child,
            'view' => 'packages/menu::partials.menu',
            'theme' => false,
            'active' => false,
        ]) !!}
    @endif
</li>
