<?php

namespace Botble\Widget\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Supports\Breadcrumb;
use Botble\Widget\Events\RenderingWidgetSettings;
use Botble\Widget\Facades\WidgetGroup;
use Botble\Widget\Models\Widget;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class WidgetController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('packages/theme::theme.appearance'))
            ->add(trans('packages/widget::widget.name'), route('widgets.index'));
    }

    public function index()
    {
        $this->pageTitle(trans('packages/widget::widget.name'));

        Assets::addScripts(['sortable'])
            ->addScriptsDirectly('vendor/core/packages/widget/js/widget.js')
            ->addStylesDirectly('vendor/core/packages/widget/css/widget.css');

        RenderingWidgetSettings::dispatch();

        $widgets = Widget::query()->where('theme', Widget::getThemeName())->get();

        $groups = WidgetGroup::getGroups();
        foreach ($widgets as $widget) {
            if (! Arr::has($groups, $widget->sidebar_id)) {
                continue;
            }

            WidgetGroup::group($widget->sidebar_id)
                ->position($widget->position)
                ->addWidget($widget->widget_id, $widget->data);
        }

        return view('packages/widget::list');
    }

    public function update(Request $request)
    {
        try {
            $sidebarId = $request->input('sidebar_id');

            $themeName = Widget::getThemeName();

            Widget::query()->where([
                'sidebar_id' => $sidebarId,
                'theme' => $themeName,
            ])->delete();

            foreach (array_filter($request->input('items', [])) as $key => $item) {

                parse_str($item, $data);

                if (empty($data['id'])) {
                    continue;
                }

                Widget::query()->create([
                    'sidebar_id' => $sidebarId,
                    'widget_id' => $data['id'],
                    'theme' => $themeName,
                    'position' => $key,
                    'data' => $data,
                ]);
            }

            $widgetAreas = Widget::query()->where([
                'sidebar_id' => $sidebarId,
                'theme' => $themeName,
            ])->get();

            return $this
                ->httpResponse()
                ->setData(view('packages/widget::item', compact('widgetAreas'))->render())
                ->setMessage(trans('packages/widget::widget.save_success'));
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            Widget::query()->where([
                'theme' => Widget::getThemeName(),
                'sidebar_id' => $request->input('sidebar_id'),
                'position' => $request->input('position'),
                'widget_id' => $request->input('widget_id'),
            ])->delete();

            $sidebarId = $request->input('sidebar_id');

            $themeName = Widget::getThemeName();

            $widgetAreas = Widget::query()->where([
                'sidebar_id' => $sidebarId,
                'theme' => $themeName,
            ])->get();

            return $this
                ->httpResponse()
                ->setData(view('packages/widget::item', compact('widgetAreas'))->render())
                ->setMessage(trans('packages/widget::widget.delete_success'));
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
