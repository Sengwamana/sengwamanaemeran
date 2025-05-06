<?php

namespace Botble\Portfolio\Http\Controllers;

use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Portfolio\Forms\ServiceForm;
use Botble\Portfolio\Http\Requests\ServiceRequest;
use Botble\Portfolio\Models\Service;
use Botble\Portfolio\Tables\ServiceTable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class ServiceController extends BaseController
{
    public function index(ServiceTable $table): View|JsonResponse
    {
        PageTitle::setTitle(trans('plugins/portfolio::portfolio.service.name'));

        return $table->renderTable();
    }

    public function create(): string
    {
        PageTitle::setTitle(trans('plugins/portfolio::portfolio.service.create'));

        return ServiceForm::create()->renderForm();
    }

    public function store(ServiceRequest $request): BaseHttpResponse
    {
        $form = ServiceForm::create();
        $form
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setNextUrl(route('portfolio.services.edit', $form->getModel()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Service $service): string
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $service->name]));

        return ServiceForm::createFromModel($service)->renderForm();
    }

    public function update(Service $service, ServiceRequest $request): BaseHttpResponse
    {
        ServiceForm::createFromModel($service)->setRequest($request)->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('portfolio.services.index'))
            ->setNextUrl(route('portfolio.services.edit', $service))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Service $service): DeleteResourceAction
    {
        return DeleteResourceAction::make($service);
    }
}
