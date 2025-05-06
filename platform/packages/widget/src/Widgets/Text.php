<?php

namespace Botble\Widget\Widgets;

use Botble\Widget\AbstractWidget;

class Text extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => trans('packages/widget::widget.widget_text'),
            'description' => trans('packages/widget::widget.widget_text_description'),
            'content' => null,
        ]);

        $widgetDirectory = $this->getWidgetDirectory();

        $this->setFrontendTemplate('packages/widget::widgets.' . $widgetDirectory . '.frontend');
        $this->setBackendTemplate('packages/widget::widgets.' . $widgetDirectory . '.backend');
    }

    public function getWidgetDirectory(): string
    {
        return 'text';
    }
}
