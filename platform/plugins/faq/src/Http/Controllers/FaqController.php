<?php

namespace Botble\Faq\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Supports\Breadcrumb;
use Botble\Faq\Forms\FaqForm;
use Botble\Faq\Http\Requests\FaqRequest;
use Botble\Faq\Models\Faq;
use Botble\Faq\Tables\FaqTable;

class FaqController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/faq::faq.name'), route('faq.index'));
    }

    public function index(FaqTable $table)
    {
        $this->pageTitle(trans('plugins/faq::faq.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/faq::faq.create'));

        return FaqForm::create()->renderForm();
    }

    public function store(FaqRequest $request)
    {
        $form = FaqForm::create()->setRequest($request);
        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousRoute('faq.index')
            ->setNextRoute('faq.edit', $form->getModel()->getKey())
            ->withCreatedSuccessMessage();
    }

    public function edit(Faq $faq)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $faq->question]));

        return FaqForm::createFromModel($faq)->renderForm();
    }

    public function update(Faq $faq, FaqRequest $request)
    {
        FaqForm::createFromModel($faq)->setRequest($request)->save();

        return $this
            ->httpResponse()
            ->setPreviousRoute('faq.index')
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Faq $faq)
    {
        return DeleteResourceAction::make($faq);
    }
}
