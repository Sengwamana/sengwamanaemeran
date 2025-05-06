@if ($config['name'] || $config['content'])
    <div class="panel panel-default">
        @if ($config['name'])
            <div class="panel-title">
                <h3>{!! BaseHelper::clean($config['name']) !!}</h3>
            </div>
        @endif

        @if ($config['content'])
            <div class="panel-content">
                <div>{!! BaseHelper::clean(shortcode()->compile($config['content'])) !!}</div>
            </div>
        @endif
    </div>
@endif
