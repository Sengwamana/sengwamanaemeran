@php
    $menuItems = DashboardMenu::getAll();
    $otherItems = $menuItems->splice(match (AdminAppearance::getContainerWidth()) {
        'container-3xl' => 8,
        'container-fluid' => 10,
        default => 7,
    });
    $otherIcon = BaseHelper::renderIcon('ti ti-dots');
@endphp

<ul @class(['navbar-nav', $navbarClass ?? null])>
    @foreach ($menuItems as $menu)
        @include('core/base::layouts.partials.navbar-nav-item', [
            'menu' => $menu,
            'autoClose' => $autoClose,
            'isNav' => true,
        ])
    @endforeach

    @foreach ($otherItems as $menu)
        @include('core/base::layouts.partials.navbar-nav-item', [
            'menu' => [...$menu, 'class' => 'd-flex d-md-none'],
            'autoClose' => $autoClose,
            'isNav' => true,
        ])
    @endforeach

    @includeWhen($otherItems->isNotEmpty(), 'core/base::layouts.partials.navbar-nav-item', [
        'menu' => [
            'id' => 'others',
            'icon' => false,
            'name' => $otherIcon,
            'title' => trans('core/base::base.panel.others'),
            'children' => $otherItems->all(),
            'url' => '#',
            'active' => $otherItems->contains('active', true),
            'priority' => 9999,
            'class' => 'd-none d-md-flex',
        ],
        'autoClose' => $autoClose,
        'isNav' => true,
        'align' => 'end',
    ])
</ul>
