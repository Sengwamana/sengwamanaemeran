<?php

namespace Botble\Portfolio\Http\Controllers;

use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Portfolio\Forms\ServiceCategoryForm;
use Botble\Portfolio\Http\Requests\ServiceCategoryRequest;
use Botble\Portfolio\Models\ServiceCategory;
use Botble\Portfolio\Tables\ServiceCategoryTable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class ServiceCategoryController extends BaseController
{
    public function index(ServiceCategoryTable $table): View|JsonResponse
    {
        PageTitle::setTitle(trans('plugins/portfolio::portfolio.service_category.name'));

        return $table->renderTable();
    }

    public function create(): string
    {
        PageTitle::setTitle(trans('plugins/portfolio::portfolio.service_category.create'));

        return ServiceCategoryForm::create()->renderForm();
    }

    public function store(ServiceCategoryRequest $request): BaseHttpResponse
    {
        $form = ServiceCategoryForm::create();
        $form
           ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setNextUrl(route('portfolio.service-categories.edit', $form->getModel()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(ServiceCategory $serviceCategory): string
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $serviceCategory->name]));

        return ServiceCategoryForm::createFromModel($serviceCategory)->renderForm();
    }

    public function update(ServiceCategory $serviceCategory, ServiceCategoryRequest $request): BaseHttpResponse
    {
        ServiceCategoryForm::createFromModel($serviceCategory)->setRequest($request)->save();

        return $this
            ->httpResponse()
            ->setNextUrl(route('portfolio.service-categories.edit', $serviceCategory))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(ServiceCategory $serviceCategory): DeleteResourceAction
    {
        return DeleteResourceAction::make($serviceCategory);
    }
}
