<ul{!! BaseHelper::clean($options) !!}>
    @foreach($menu_nodes as $key => $row)
        <li @class(['nav-item', 'has-children' => $row->has_child, 'active' => $row->active])>
            <a @class(['nav-link', $row->css_class]) href="{{ $row->url }}" target="{{ $row->target }}">
                {!! BaseHelper::clean($row->icon_html) !!}
                {{ $row->title }}
            </a>

            @if($row->has_child)
                <span class="menu-expand"><i class="arrow-small-down"></i></span>

                {!! Menu::renderMenuLocation('main-menu', [
                    'view' => 'main-menu',
                    'menu' => $menu,
                    'menu_nodes' => $row->child,
                    'options' => ['class' => 'sub-menu'],
                ]) !!}
            @endif
        </li>
    @endforeach
</ul>
