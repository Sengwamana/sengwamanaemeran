@if (empty($widgetSetting) || $widgetSetting->status == 1)
    <x-core::stat-widget.item
        :label="$widget->title"
        :value="is_int($widget->statsTotal) ? number_format($widget->statsTotal) : $widget->statsTotal"
        :url="$widget->route"
        :icon="$widget->icon"
        :color="$widget->color"
        :column="$widget->column"
    />
@endif
