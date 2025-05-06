<?php

namespace Botble\Portfolio\Listeners;

use Botble\Portfolio\Models\Package;
use Botble\Portfolio\Models\Project;
use Botble\Portfolio\Models\Service;
use Botble\Portfolio\Models\ServiceCategory;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Botble\Theme\Facades\SiteMapManager;

class PortfolioSiteMapListener
{
    public function handle(RenderingSiteMapEvent $event): void
    {
        $projectLastUpdated = Project::query()
            ->wherePublished()
            ->latest('updated_at')
            ->value('updated_at');

        if ($event->key == 'projects') {
            $projects = Project::query()
                ->with('slugable')
                ->wherePublished()
                ->select(['id', 'name', 'updated_at'])->latest()
                ->get();

            foreach ($projects as $project) {
                SiteMapManager::add($project->url, $project->updated_at, '0.8');
            }

            return;
        }

        SiteMapManager::addSitemap(SiteMapManager::route('projects'), $projectLastUpdated);

        $serviceCategoryLastUpdated = ServiceCategory::query()
            ->wherePublished()
            ->latest('updated_at')
            ->value('updated_at');

        if ($event->key == 'service-categories') {
            $serviceCategories = ServiceCategory::query()
                ->with('slugable')
                ->wherePublished()
                ->select(['id', 'name', 'updated_at'])->latest()
                ->get();

            foreach ($serviceCategories as $serviceCategory) {
                SiteMapManager::add($serviceCategory->url, $serviceCategory->updated_at, '0.8');
            }

            return;
        }

        SiteMapManager::addSitemap(SiteMapManager::route('service-categories'), $serviceCategoryLastUpdated);

        $serviceLastUpdated = Service::query()
            ->wherePublished()
            ->latest('updated_at')
            ->value('updated_at');

        if ($event->key == 'services') {
            $services = Service::query()
                ->with('slugable')
                ->wherePublished()
                ->select(['id', 'name', 'updated_at'])->latest()
                ->get();

            foreach ($services as $service) {
                SiteMapManager::add($service->url, $service->updated_at, '0.8');
            }

            return;
        }

        SiteMapManager::addSitemap(SiteMapManager::route('services'), $serviceLastUpdated);

        $packageLastUpdated = Package::query()
            ->wherePublished()
            ->latest('updated_at')
            ->value('updated_at');

        if ($event->key == 'packages') {
            $packages = Package::query()
                ->with('slugable')
                ->wherePublished()
                ->select(['id', 'name', 'updated_at'])->latest()
                ->get();

            foreach ($packages as $package) {
                SiteMapManager::add($package->url, $package->updated_at, '0.8');
            }

            return;
        }

        SiteMapManager::addSitemap(SiteMapManager::route('packages'), $packageLastUpdated);
    }
}
