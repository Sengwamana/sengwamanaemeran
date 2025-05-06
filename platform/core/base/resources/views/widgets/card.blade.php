<div
    id="{{ $id . '-parent' }}"
    @class(['widget-item', 'col-md-' . $columns => $columns])
>
    <div class="h-100 position-relative">
        {!! $content !!}
        @if ($hasChart)
            <div
                class="position-absolute fixed-bottom"
                id="{{ $id }}"
                style="z-index: 1"
            ></div>
        @endif
    </div>

    @if ($hasChart)
        @include('core/base::widgets.partials.chart-script')
    @endif
</div>
