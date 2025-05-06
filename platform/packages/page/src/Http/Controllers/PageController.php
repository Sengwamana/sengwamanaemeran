<?php

namespace Botble\Page\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Supports\Breadcrumb;
use Botble\Page\Forms\PageForm;
use Botble\Page\Http\Requests\PageRequest;
use Botble\Page\Models\Page;
use Botble\Page\Tables\PageTable;

class PageController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('packages/page::pages.menu_name'), route('pages.index'));
    }

    public function index(PageTable $pageTable)
    {
        $this->pageTitle(trans('packages/page::pages.menu_name'));

        return $pageTable->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('packages/page::pages.create'));

        return PageForm::create()->renderForm();
    }

    public function store(PageRequest $request)
    {
        $form = PageForm::create()
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousRoute('pages.index')
            ->setNextRoute('pages.edit', $form->getModel()->getKey())
            ->withCreatedSuccessMessage();
    }

    public function edit(Page $page)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $page->name]));

        return PageForm::createFromModel($page)->renderForm();
    }

    public function update(Page $page, PageRequest $request)
    {
        PageForm::createFromModel($page)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousRoute('pages.index')
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Page $page): DeleteResourceAction
    {
        return DeleteResourceAction::make($page);
    }
}
