<?php

namespace Botble\Portfolio\Http\Controllers;

use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Portfolio\Enums\QuoteStatus;
use Botble\Portfolio\Forms\QuoteForm;
use Botble\Portfolio\Models\Quote;
use Botble\Portfolio\Tables\QuoteTable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuotationRequestController extends BaseController
{
    public function index(QuoteTable $table): View|JsonResponse
    {
        PageTitle::setTitle(trans('plugins/portfolio::portfolio.quotation_request.name'));

        return $table->renderTable();
    }

    public function edit(Quote $quotationRequest): string
    {
        PageTitle::setTitle(trans('plugins/portfolio::portfolio.quotation_request.viewing', ['name' => $quotationRequest->getKey()]));

        return QuoteForm::createFromModel($quotationRequest)->renderForm();
    }

    public function update(Quote $quotationRequest, Request $request): BaseHttpResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(QuoteStatus::values())],
        ]);

        QuoteForm::createFromModel($quotationRequest)
            ->saving(function (QuoteForm $form) use ($validated): void {
                $model = $form->getModel();
                $model->update($validated);
            });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('portfolio.quotation-requests.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Quote $quotationRequest): DeleteResourceAction
    {
        return DeleteResourceAction::make($quotationRequest);
    }
}
