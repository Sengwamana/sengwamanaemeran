<?php

namespace Botble\RequestLog\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseSystemController;
use Botble\RequestLog\Models\RequestLog;
use Botble\RequestLog\Tables\RequestLogTable;
use Illuminate\Http\Request;

class RequestLogController extends BaseSystemController
{
    public function getWidgetRequestErrors(Request $request)
    {
        $limit = $request->integer('paginate', 10);
        $limit = $limit > 0 ? $limit : 10;

        $requests = RequestLog::query()
            ->orderByDesc('created_at')
            ->paginate($limit);

        return $this
            ->httpResponse()
            ->setData(view('plugins/request-log::widgets.request-errors', compact('requests', 'limit'))->render());
    }

    public function index(RequestLogTable $dataTable)
    {
        Assets::addScriptsDirectly('vendor/core/plugins/request-log/js/request-log.js');

        $this->pageTitle(trans('plugins/request-log::request-log.name'));

        return $dataTable->renderTable();
    }

    public function destroy(RequestLog $log)
    {
        return DeleteResourceAction::make($log);
    }

    public function deleteAll()
    {
        RequestLog::query()->truncate();

        return $this
            ->httpResponse()
            ->withDeletedSuccessMessage();
    }
}
