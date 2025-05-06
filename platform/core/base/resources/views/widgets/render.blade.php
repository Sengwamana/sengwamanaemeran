<div class="row row-cards">
    @foreach ($widgets as $widget)
        {{ $widget->render() }}
    @endforeach
</div>
