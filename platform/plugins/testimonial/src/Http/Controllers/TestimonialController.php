<?php

namespace Botble\Testimonial\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Supports\Breadcrumb;
use Botble\Testimonial\Forms\TestimonialForm;
use Botble\Testimonial\Http\Requests\TestimonialRequest;
use Botble\Testimonial\Models\Testimonial;
use Botble\Testimonial\Tables\TestimonialTable;

class TestimonialController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/testimonial::testimonial.name'), route('testimonial.index'));
    }

    public function index(TestimonialTable $table)
    {
        $this->pageTitle(trans('plugins/testimonial::testimonial.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/testimonial::testimonial.create'));

        return TestimonialForm::create()->renderForm();
    }

    public function store(TestimonialRequest $request)
    {
        $form = TestimonialForm::create()->setRequest($request);
        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousRoute('testimonial.index')
            ->setNextRoute('testimonial.edit', $form->getModel()->getKey())
            ->withCreatedSuccessMessage();
    }

    public function edit(Testimonial $testimonial)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $testimonial->name]));

        return TestimonialForm::createFromModel($testimonial)->renderForm();
    }

    public function update(Testimonial $testimonial, TestimonialRequest $request)
    {
        TestimonialForm::createFromModel($testimonial)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousRoute('testimonial.index')
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Testimonial $testimonial)
    {
        return DeleteResourceAction::make($testimonial);
    }
}
