<?php

namespace Botble\Blog\Http\Controllers;

use Botble\ACL\Models\User;
use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Requests\UpdateTreeCategoryRequest;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Breadcrumb;
use Botble\Base\Supports\RepositoryHelper;
use Botble\Blog\Forms\CategoryForm;
use Botble\Blog\Http\Requests\CategoryRequest;
use Botble\Blog\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/blog::base.menu_name'))
            ->add(trans('plugins/blog::categories.menu'), route('categories.index'));
    }

    public function index(Request $request)
    {
        $this->pageTitle(trans('plugins/blog::categories.menu'));

        $categories = Category::query()
            ->orderByDesc('is_default')
            ->oldest('order')->oldest()
            ->with('slugable');

        $categories = RepositoryHelper::applyBeforeExecuteQuery($categories, new Category())->get();

        if ($request->ajax()) {
            $data = view('core/base::forms.partials.tree-categories', $this->getOptions(compact('categories')))
                ->render();

            return $this
                ->httpResponse()
                ->setData($data);
        }

        Assets::addStylesDirectly('vendor/core/core/base/css/tree-category.css')
            ->addScriptsDirectly('vendor/core/core/base/js/tree-category.js');

        $form = CategoryForm::create(['template' => 'core/base::forms.form-tree-category']);
        $form = $this->setFormOptions($form, null, compact('categories'));

        return $form->renderForm();
    }

    public function create(Request $request)
    {
        $this->pageTitle(trans('plugins/blog::categories.create'));

        if ($request->ajax()) {
            return $this
                ->httpResponse()
                ->setData($this->getForm());
        }

        return CategoryForm::create()->renderForm();
    }

    public function store(CategoryRequest $request)
    {
        if ($request->input('is_default')) {
            Category::query()->where('id', '>', 0)->update(['is_default' => 0]);
        }

        $form = CategoryForm::create();
        $form
            ->saving(function (CategoryForm $form) use ($request): void {
                $form
                    ->getModel()
                    ->fill([...$request->validated(),
                        'author_id' => Auth::guard()->id(),
                        'author_type' => User::class,
                    ])
                    ->save();
            });

        $response = $this->httpResponse();

        /**
         * @var Category $category
         */
        $category = $form->getModel();

        if ($request->ajax()) {
            if ($response->isSaving()) {
                $form = $this->getForm();
            } else {
                $form = $this->getForm($category);
            }

            $response->setData([
                'model' => $category,
                'form' => $form,
            ]);
        }

        return $response
            ->setPreviousRoute('categories.index')
            ->setNextRoute('categories.edit', $category->getKey())
            ->withCreatedSuccessMessage();
    }

    public function edit(Category $category, Request $request)
    {
        if ($request->ajax()) {
            return $this
                ->httpResponse()
                ->setData($this->getForm($category));
        }

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $category->name]));

        return CategoryForm::createFromModel($category)->renderForm();
    }

    public function update(Category $category, CategoryRequest $request)
    {
        if ($request->input('is_default')) {
            Category::query()->where('id', '!=', $category->getKey())->update(['is_default' => 0]);
        }

        CategoryForm::createFromModel($category)->save();

        $response = $this->httpResponse();

        if ($request->ajax()) {
            if ($response->isSaving()) {
                $form = $this->getForm();
            } else {
                $form = $this->getForm($category);
            }

            $response->setData([
                'model' => $category,
                'form' => $form,
            ]);
        }

        return $response
            ->setPreviousRoute('categories.index')
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Category $category)
    {
        return DeleteResourceAction::make($category);
    }

    public function updateTree(UpdateTreeCategoryRequest $request): BaseHttpResponse
    {
        Category::updateTree($request->validated('data'));

        return $this
            ->httpResponse()
            ->withUpdatedSuccessMessage();
    }

    protected function getForm(?Category $model = null): string
    {
        $options = ['template' => 'core/base::forms.form-no-wrap'];

        if ($model) {
            $options['model'] = $model;
        }

        $form = CategoryForm::create($options);

        $form = $this->setFormOptions($form, $model);

        return $form->renderForm();
    }

    protected function setFormOptions(FormAbstract $form, ?Category $model = null, array $options = []): FormAbstract
    {
        if (! $model) {
            $form->setUrl(route('categories.create'));
        }

        if (! Auth::guard()->user()->hasPermission('categories.create') && ! $model) {
            $class = $form->getFormOption('class');
            $form->setFormOption('class', $class . ' d-none');
        }

        $form->setFormOptions($this->getOptions($options));

        return $form;
    }

    protected function getOptions(array $options = []): array
    {
        return array_merge([
            'canCreate' => Auth::guard()->user()->hasPermission('categories.create'),
            'canEdit' => Auth::guard()->user()->hasPermission('categories.edit'),
            'canDelete' => Auth::guard()->user()->hasPermission('categories.destroy'),
            'createRoute' => 'categories.create',
            'editRoute' => 'categories.edit',
            'deleteRoute' => 'categories.destroy',
            'updateTreeRoute' => 'categories.update-tree',
        ], $options);
    }
}
