<?php

namespace ArchiElite\Announcement\Http\Controllers;

use ArchiElite\Announcement\Forms\AnnouncementForm;
use ArchiElite\Announcement\Http\Requests\AnnouncementRequest;
use ArchiElite\Announcement\Models\Announcement;
use ArchiElite\Announcement\Tables\AnnouncementTable;
use Botble\Base\Events\BeforeUpdateContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Breadcrumb;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class AnnouncementController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/announcement::announcements.name'), route('announcements.index'));
    }

    public function index(AnnouncementTable $table): View|JsonResponse
    {
        $this->pageTitle(trans('plugins/announcement::announcements.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder): string
    {
        $this->pageTitle(trans('plugins/announcement::announcements.create'));

        return $formBuilder->create(AnnouncementForm::class)->renderForm();
    }

    public function store(AnnouncementRequest $request): BaseHttpResponse
    {
        $announcement = Announcement::query()->create($request->validated());

        event(new CreatedContentEvent('announcement', $request, $announcement));

        return $this->httpResponse()
            ->setPreviousUrl(route('announcements.index'))
            ->setNextUrl(route('announcements.edit', $announcement->getKey()))
            ->withCreatedSuccessMessage();
    }

    public function edit(Announcement $announcement, FormBuilder $formBuilder): string
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $announcement->name]));

        return $formBuilder->create(AnnouncementForm::class, ['model' => $announcement])->renderForm();
    }

    public function update(Announcement $announcement, AnnouncementRequest $request): BaseHttpResponse
    {
        event(new BeforeUpdateContentEvent($request, $announcement));

        $announcement->update($request->validated());

        event(new UpdatedContentEvent('announcement', $request, $announcement));

        return $this->httpResponse()
            ->setPreviousUrl(route('announcements.index'))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Announcement $announcement): DeleteResourceAction
    {
        return DeleteResourceAction::make($announcement);
    }
}
