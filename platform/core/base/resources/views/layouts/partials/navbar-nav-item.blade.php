@php
    $menu = apply_filters(BASE_FILTER_DASHBOARD_MENU, $menu);

    if(in_array($menu['id'], ['cms-core-settings', 'cms-core-system'], true)) {
        $menu['children'] = [];
    }

    $hasChildren = array_key_exists('children', $menu) && ($childrenCount = count($menu['children']));
    $children = $hasChildren ? $menu['children'] : [];

    $autoClose ??= 'outside';
    $align ??= 'start';
@endphp

<li @class([
    'nav-item',
    'active' => $menu['active'],
    'dropdown' => $hasChildren,
    $menu['class'] ?? null,
])>
    @include('core/base::layouts.partials.navbar-nav-item-link', [
        'menu' => $menu,
        'hasChildren' => $hasChildren,
        'autoClose' => $autoClose,
    ])

    @php
        $alignClass = match ($align) {
            'start' => 'dropdown-menu-start',
            'end' => 'dropdown-menu-end',
            default => null,
        };
    @endphp

    @if ($hasChildren)
        <div @class([
            'dropdown-menu animate slideIn',
            $alignClass,
            'show' => $menu['active'] && $autoClose === 'false',
        ])>
                @foreach($children as $child)
                    @php
                        if(in_array($child['id'], ['cms-core-settings', 'cms-core-system', 'cms-core-platform-administration'], true)) {
                            $child['children'] = [];
                        }

                        $childHasChildren = array_key_exists('children', $child) && count($child['children']);
                    @endphp

                    @if($childHasChildren)
                        <div class="dropdown">
                    @endif

                    @include('core/base::layouts.partials.navbar-nav-item-link', [
                        'menu' => $child,
                        'hasChildren' => $childHasChildren,
                        'autoClose' => $autoClose,
                        'isNav' => false,
                    ])

                    @if($childHasChildren)
                            <div
                                @class([
                                    'dropdown-menu animate slideIn',
                                    'show' => $child['active'] && $autoClose === 'false',
                                ])
                            >
                                @foreach ($child['children'] as $childItem)
                                    @include('core/base::layouts.partials.navbar-nav-item-link', [
                                        'menu' => $childItem,
                                        'hasChildren' => false,
                                        'autoClose' => $autoClose,
                                        'isNav' => false,
                                    ])
                                @endforeach
                            </div>
                        </div>
                    @endif
            @endforeach
        </div>
    @endif
</li>
