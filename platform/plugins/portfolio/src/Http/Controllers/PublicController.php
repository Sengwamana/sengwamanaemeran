<?php

namespace Botble\Portfolio\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Portfolio\Enums\CustomFieldType;
use Botble\Portfolio\Enums\QuoteStatus;
use Botble\Portfolio\Http\Requests\QuoteRequest;
use Botble\Portfolio\Models\CustomField;
use Botble\Portfolio\Models\Package;
use Botble\Portfolio\Models\Project;
use Botble\Portfolio\Models\Quote;
use Botble\Portfolio\Models\Service;
use Botble\Portfolio\Models\ServiceCategory;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\SeoHelper\SeoOpenGraph;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\AdminBar;
use Botble\Theme\Facades\Theme;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class PublicController extends BaseController
{
    public function services(): Response
    {
        SeoHelper::setTitle(__('Services'));

        $services = Service::query()
            ->wherePublished()
            ->oldest('order')
            ->latest()
            ->paginate(10);

        return Theme::scope('portfolio.services', compact('services'))->render();
    }

    public function category(string $key)
    {
        $slug = SlugHelper::getSlug($key, SlugHelper::getPrefix(ServiceCategory::class));

        abort_unless($slug, 404);

        $category = ServiceCategory::query()->with(['services', 'services.metadata', 'services.slugable']);

        if (! Auth::guard()->check() || ! request()->input('preview')) {
            $category = $category->wherePublished();
        }

        $category = $category
            ->findOrFail($slug->reference_id);

        SeoHelper::setTitle($category->name)
            ->setDescription($category->description);

        SeoHelper::setSeoOpenGraph(
            (new SeoOpenGraph())
                ->setDescription($category->description)
                ->setUrl($category->url)
                ->setTitle($category->name)
                ->setType('article')
        );

        Theme::breadcrumb()->add($category->name, $category->url);

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, 'portfolio', $category);

        if (function_exists('admin_bar')) {
            AdminBar::registerLink(
                trans('plugins/portfolio::portfolio.edit_this_service_category'),
                route('portfolio.service-categories.edit', $category->id),
                null,
                'portfolio.service-categories.edit'
            );
        }

        return Theme::scope('portfolio.category', compact('category'))->render();
    }

    public function service(string $key): Response
    {
        $slug = SlugHelper::getSlug($key, SlugHelper::getPrefix(Service::class));

        abort_unless($slug, 404);

        $service = Service::query();

        if (! Auth::guard()->check() || ! request()->input('preview')) {
            $service = $service->wherePublished();
        }

        $service = $service->findOrFail($slug->reference_id);

        SeoHelper::setTitle($service->name)
            ->setDescription($service->description);

        SeoHelper::setSeoOpenGraph(
            (new SeoOpenGraph())
                ->setDescription($service->description)
                ->setUrl($service->url)
                ->setTitle($service->name)
                ->setType('article')
        );

        Theme::breadcrumb()->add($service->name, $service->url);

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, 'portfolio', $service);

        $relatedServices = Service::query()
            ->wherePublished()
            ->where('id', '<>', $service->id)
            ->with('metadata')
            ->inRandomOrder()
            ->limit(3)
            ->get();

        if (function_exists('admin_bar')) {
            AdminBar::registerLink(
                trans('plugins/portfolio::portfolio.edit_this_service'),
                route('portfolio.services.edit', $service->id),
                null,
                'portfolio.services.edit'
            );
        }

        return Theme::scope('portfolio.service', compact('service', 'relatedServices'))->render();
    }

    public function package(string $key): Response
    {
        $slug = SlugHelper::getSlug($key, SlugHelper::getPrefix(Package::class));

        abort_unless($slug, 404);

        $package = Package::query();

        if (! Auth::guard()->check() || ! request()->input('preview')) {
            $package = $package->wherePublished();
        }

        $package = $package->findOrFail($slug->reference_id);

        if (function_exists('admin_bar')) {
            AdminBar::registerLink(
                trans('plugins/portfolio::portfolio.edit_this_package'),
                route('portfolio.packages.edit', $package->getKey()),
                null,
                'portfolio.packages.edit'
            );
        }

        return Theme::scope('portfolio.package', compact('package'))->render();
    }

    public function project(string $key): Response
    {
        $slug = SlugHelper::getSlug($key, SlugHelper::getPrefix(Project::class));

        abort_unless($slug, 404);

        $project = Project::query()
            ->wherePublished()
            ->findOrFail($slug->reference_id);

        SeoHelper::setTitle($project->name)
            ->setDescription($project->description);

        SeoHelper::setSeoOpenGraph(
            (new SeoOpenGraph())
                ->setDescription($project->description)
                ->setUrl($project->url)
                ->setTitle($project->name)
                ->setType('article')
        );

        Theme::breadcrumb()->add($project->name, $project->url);

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, 'portfolio', $project);

        if (function_exists('admin_bar')) {
            AdminBar::registerLink(
                trans('plugins/portfolio::portfolio.edit_this_project'),
                route('portfolio.projects.edit', $project->id),
                null,
                'portfolio.projects.edit'
            );
        }

        return Theme::scope('portfolio.project', compact('project'))->render();
    }

    public function storeQuote(QuoteRequest $request, BaseHttpResponse $response): BaseHttpResponse
    {
        $customFields = $request->input('custom_fields', []);
        $fields = [];

        if (! empty($customFields)) {
            CustomField::query()
                ->whereIn('id', array_keys($customFields))
                ->select(['id', 'name', 'type'])
                ->with('options')
                ->get()
                ->map(function (CustomField $customField) use (&$fields, $customFields) {
                    $option = $customField->options->where('id', Arr::get($customFields, $customField->getKey()))->pluck('label');

                    if ($customField->type == CustomFieldType::CHECKBOX) {
                        return $fields[$customField->name] = implode(', ', $customFields[$customField->getKey()]);
                    }

                    if ($customField->type == CustomFieldType::TEXT || $customField->type == CustomFieldType::TEXTAREA || $customField->type == CustomFieldType::NUMBER) {
                        return $fields[$customField->name] = Arr::get($customFields, $customField->getKey());
                    }

                    return $fields[$customField->name] = $option->first();
                });
        }

        try {
            $quote = Quote::query()->create(array_merge($request->validated(), [
                'fields' => $fields,
                'status' => QuoteStatus::UNREAD,
            ]));

            EmailHandler::setModule('portfolio')
                ->setVariableValues([
                    'site_name' => config('app.name'),
                    'contact_name' => $quote->name,
                    'contact_email' => $quote->email,
                    'contact_message' => $quote->message,
                    'fields' => $quote->fields ?? [],
                ])
                ->sendUsingTemplate('quote-request-notice');
        } catch (Exception $e) {
            BaseHelper::logError($e);

            return $response
                ->setError()
                ->setMessage(__('An error occurred. Please try again later.'));
        }

        return $response
            ->setMessage(__('Thank you for your quote request. We will contact you soon.'));
    }
}
