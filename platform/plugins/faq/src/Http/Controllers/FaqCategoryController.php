<?php

namespace Botble\Faq\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Supports\Breadcrumb;
use Botble\Faq\Forms\FaqCategoryForm;
use Botble\Faq\Http\Requests\FaqCategoryRequest;
use Botble\Faq\Models\FaqCategory;
use Botble\Faq\Tables\FaqCategoryTable;

class FaqCategoryController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/faq::faq.name'))
            ->add(trans('plugins/faq::faq-category.name'), route('faq_category.index'));
    }

    public function index(FaqCategoryTable $table)
    {
        $this->pageTitle(trans('plugins/faq::faq-category.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/faq::faq-category.create'));

        return FaqCategoryForm::create()->renderForm();
    }

    public function store(FaqCategoryRequest $request)
    {
        $form = FaqCategoryForm::create()->setRequest($request);
        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousRoute('faq_category.index')
            ->setNextRoute('faq_category.edit', $form->getModel()->getKey())
            ->withCreatedSuccessMessage();
    }

    public function edit(FaqCategory $faqCategory)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $faqCategory->name]));

        return FaqCategoryForm::createFromModel($faqCategory)->renderForm();
    }

    public function update(FaqCategory $faqCategory, FaqCategoryRequest $request)
    {
        FaqCategoryForm::createFromModel($faqCategory)->setRequest($request)->save();

        return $this
            ->httpResponse()
            ->setPreviousRoute('faq_category.index')
            ->withUpdatedSuccessMessage();
    }

    public function destroy(FaqCategory $faqCategory)
    {
        return DeleteResourceAction::make($faqCategory);
    }
}
