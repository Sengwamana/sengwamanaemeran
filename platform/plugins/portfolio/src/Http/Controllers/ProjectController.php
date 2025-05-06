<?php

namespace Botble\Portfolio\Http\Controllers;

use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Portfolio\Forms\ProjectForm;
use Botble\Portfolio\Http\Requests\ProjectRequest;
use Botble\Portfolio\Models\Project;
use Botble\Portfolio\Tables\ProjectTable;

class ProjectController extends BaseController
{
    public function index(ProjectTable $dataTable)
    {
        PageTitle::setTitle(trans('plugins/portfolio::portfolio.project.name'));

        return $dataTable->renderTable();
    }

    public function create(): string
    {
        PageTitle::setTitle(trans('plugins/portfolio::portfolio.project.create'));

        return ProjectForm::create()->renderForm();
    }

    public function edit(Project $project): string
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $project->name]));

        return ProjectForm::createFromModel($project)->renderForm();
    }

    public function store(ProjectRequest $request): BaseHttpResponse
    {
        $form = ProjectForm::create();
        $form
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setNextUrl(route('portfolio.projects.edit', $form->getModel()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function update(Project $project, ProjectRequest $request): BaseHttpResponse
    {
        ProjectForm::createFromModel($project)->setRequest($request)->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('portfolio.projects.index'))
            ->setNextUrl(route('portfolio.projects.edit', $project))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Project $project): DeleteResourceAction
    {
        return DeleteResourceAction::make($project);
    }
}
