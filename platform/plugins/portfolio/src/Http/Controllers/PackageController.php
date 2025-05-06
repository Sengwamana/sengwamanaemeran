<?php

namespace Botble\Portfolio\Http\Controllers;

use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Portfolio\Forms\PackageForm;
use Botble\Portfolio\Http\Requests\PackageRequest;
use Botble\Portfolio\Models\Package;
use Botble\Portfolio\Tables\PackageTable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class PackageController extends BaseController
{
    public function index(PackageTable $table): View|JsonResponse
    {
        PageTitle::setTitle(trans('plugins/portfolio::portfolio.package.name'));

        return $table->renderTable();
    }

    public function create(): string
    {
        PageTitle::setTitle(trans('plugins/portfolio::portfolio.package.create'));

        return PackageForm::create()->renderForm();
    }

    public function store(PackageRequest $request): BaseHttpResponse
    {
        $form = PackageForm::create();
        $form
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setNextUrl(route('portfolio.packages.edit', $form->getModel()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Package $package): string
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $package->name]));

        return PackageForm::createFromModel($package)->renderForm();
    }

    public function update(Package $package, PackageRequest $request): BaseHttpResponse
    {
        PackageForm::createFromModel($package)->setRequest($request)->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('portfolio.packages.index'))
            ->setNextUrl(route('portfolio.packages.edit', $package))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Package $package): DeleteResourceAction
    {
        return DeleteResourceAction::make($package);
    }
}
