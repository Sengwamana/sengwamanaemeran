<?php

namespace Botble\Portfolio\Http\Controllers;

use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Portfolio\Forms\CustomFieldForm;
use Botble\Portfolio\Http\Requests\CustomFieldRequest;
use Botble\Portfolio\Http\Resources\CustomFieldResource;
use Botble\Portfolio\Models\CustomField;
use Botble\Portfolio\Tables\CustomFieldTable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomFieldController extends BaseController
{
    public function index(CustomFieldTable $table): View|JsonResponse
    {
        PageTitle::setTitle(trans('plugins/portfolio::portfolio.custom_field.name'));

        return $table->renderTable();
    }

    public function create(): string
    {
        PageTitle::setTitle(trans('plugins/portfolio::portfolio.custom_field.create'));

        return CustomFieldForm::create()->renderForm();
    }

    public function store(CustomFieldRequest $request): BaseHttpResponse
    {
        $form = CustomFieldForm::create();

        $form->saving(function (CustomFieldForm $form) use ($request): void {
            $model = $form->getModel();

            $model->fill([...$request->validated(),
                'author_type' => $request->user()::class,
                'author_id' => $request->user()->getKey(),
            ]);

            $model->save();

            if (! empty($options = $request->input('options', []))) {
                $model->saveOptions($options);
            }
        });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('portfolio.custom-fields.index'))
            ->setNextUrl(route('portfolio.custom-fields.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(CustomField $customField): string
    {
        $customField->loadMissing('options');

        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $customField->name]));

        return CustomFieldForm::createFromModel($customField)->renderForm();
    }

    public function update(CustomField $customField, CustomFieldRequest $request): BaseHttpResponse
    {
        CustomFieldForm::createFromModel($customField)->setRequest($request)
            ->saving(function (CustomFieldForm $form) use ($request): void {
                $model = $form->getModel();

                $model->update($request->validated());

                $model->saveOptions($request->input('options', []));
            });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('portfolio.custom-fields.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(CustomField $customField): DeleteResourceAction
    {
        return DeleteResourceAction::make($customField);
    }

    public function getInfo(Request $request): CustomFieldResource
    {
        $customField = CustomField::query()
            ->with(['options'])
            ->findOrFail($request->input('id'));

        return new CustomFieldResource($customField);
    }
}
