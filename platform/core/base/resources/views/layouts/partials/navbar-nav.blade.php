<ul @class(['navbar-nav', $navbarClass ?? null])>
    @foreach (DashboardMenu::getAll() as $menu)
        @include('core/base::layouts.partials.navbar-nav-item', [
            'menu' => $menu,
            'autoClose' => $autoClose,
            'isNav' => true,
        ])
    @endforeach
</ul>
