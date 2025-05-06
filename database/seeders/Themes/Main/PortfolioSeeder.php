<?php

namespace Database\Seeders\Themes\Main;

use Botble\Base\Facades\MetaBox;
use Botble\Portfolio\Enums\PackageDuration;
use Botble\Portfolio\Models\Package;
use Botble\Portfolio\Models\Project;
use Botble\Portfolio\Models\Service;
use Botble\Portfolio\Models\ServiceCategory;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Database\Seeders\ThemeSeeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PortfolioSeeder extends ThemeSeeder
{
    public function run(): void
    {
        $this->uploadFiles('projects', 'main');

        ServiceCategory::query()->truncate();
        Service::query()->truncate();
        Project::query()->truncate();
        Package::query()->truncate();
        DB::table('pf_service_categories_translations')->truncate();
        DB::table('pf_services_translations')->truncate();
        DB::table('pf_projects_translations')->truncate();
        DB::table('pf_packages_translations')->truncate();

        foreach ($this->getCategories() as $category) {
            ServiceCategory::query()->create($category);
        }

        $categories = ServiceCategory::query()->pluck('id');

        foreach ($this->getServices() as $service) {
            $metadata = Arr::pull($service, 'metadata', []);

            $service = Service::query()->create([
                ...$service,
                'category_id' => $categories->random(),
                'content' => File::get(database_path('seeders/contents/project.html')),
                'is_featured' => $this->fake()->boolean(),
                'views' => $this->fake()->numberBetween(0, 10000),
            ]);

            foreach ($metadata as $key => $item) {
                MetaBox::saveMetaBoxData($service, $key, $item);
            }

            SlugHelper::createSlug($service);
        }

        foreach ($this->getProjects() as $index => $project) {
            $index++;

            $metadata = Arr::pull($project, 'metadata', []);

            $project = Project::query()->create([
                ...$project,
                'content' => File::get(database_path('seeders/contents/project.html')),
                'image' => $this->filePath("projects/$index.png", 'main'),
                'is_featured' => $this->fake()->boolean(),
                'views' => $this->fake()->numberBetween(0, 10000),
            ]);

            foreach ($metadata as $key => $item) {
                MetaBox::saveMetaBoxData($project, $key, $item);
            }

            SlugHelper::createSlug($project);
        }

        $packages = [
            [
                'name' => 'Basic',
                'description' => 'For small businesses and startups.',
                'price' => '$49',
                'duration' => PackageDuration::HOURLY,
                'features' => <<<HTML
                Require your wireframe
                Design using Figma, Framer
                Develop with Webflow, React, WordPress, Laravel/PHP
                Remote/Online collaboration
                Available on business days, no weekends
                6 months of support
                HTML,
            ],
            [
                'name' => 'Business',
                'description' => 'For growing businesses and agencies.',
                'price' => '$99',
                'duration' => PackageDuration::HOURLY,
                'features' => <<<HTML
                No wireframe needed
                Design using Figma, Framer
                Develop with Webflow, React, WordPress, Laravel/PHP
                Remote/Online collaboration
                Available on business days, no weekends
                12 months of support
                Your project is always a priority
                Customer care gifts included
                HTML,
            ],
        ];

        foreach ($packages as $item) {
            Package::query()->create([
                ...$item,
                'content' => '',
                'action_label' => 'Get Started',
                'action_url' => '/contact',
            ]);
        }
    }

    public function getCategories(): array
    {
        return [
            [
                'name' => 'Development',
                'description' => 'All types of software and web development services.',
            ],
            [
                'name' => 'Design',
                'description' => 'Creative and intuitive design solutions for UI/UX and branding.',
            ],
            [
                'name' => 'Marketing',
                'description' => 'Digital marketing services, including SEO, social media, and more.',
            ],
            [
                'name' => 'Content',
                'description' => 'Content creation and management for blogs, websites, and media.',
            ],
        ];
    }

    public function getServices(): array
    {
        $this->uploadFiles('services');

        return [
            [
                'name' => 'Web Development',
                'description' => 'Building responsive and modern websites using cutting-edge <br> technologies.',
                'image' => $this->filePath('services/1.png'),
            ],
            [
                'name' => 'UI/UX Design',
                'description' => 'Creating user-centered designs that provide intuitive and <br> engaging user experiences.',
                'image' => $this->filePath('services/2.png'),
            ],
            [
                'name' => 'SEO Optimization',
                'description' => 'Improving website rankings on search engines to drive more <br> organic traffic.',
                'image' => $this->filePath('services/3.png'),
            ],
            [
                'name' => 'Content Creation',
                'description' => 'Providing high-quality content tailored to your business <br> needs.',
                'image' => $this->filePath('services/4.png'),
            ],
        ];
    }

    public function getProjects(): array
    {
        $this->uploadFiles('projects');

        return [
            [
                'name' => 'E-Commerce Website',
                'description' => 'A full-featured e-commerce platform with a clean UI/UX, integrated payment systems, and advanced search features.',
                'client' => 'Retail Store',
                'start_date' => '2023-08-15',
                'metadata' => [
                    'link' => 'https://example.com/e-commerce',
                    'category_ids' => [1],
                ],
            ],
            [
                'name' => 'Mobile App Design',
                'description' => 'A sleek mobile app design for a travel booking service, focusing on user-friendly navigation and visual appeal.',
                'client' => 'Travel Agency',
                'start_date' => '2023-05-20',
                'metadata' => [
                    'link' => 'https://example.com/mobile-app',
                    'category_ids' => [2],
                ],
            ],
            [
                'name' => 'Portfolio Website',
                'description' => 'A minimalist portfolio website for showcasing creative work, with fast loading and responsive design.',
                'client' => 'Creative Professional',
                'start_date' => '2022-11-10',
                'metadata' => [
                    'link' => 'https://example.com/portfolio',
                    'category_ids' => [1, 2],
                ],
            ],
            [
                'name' => 'SEO and Marketing Campaign',
                'description' => 'A successful SEO and digital marketing campaign, driving organic traffic and increasing conversion rates.',
                'client' => 'Tech Startup',
                'start_date' => '2023-02-05',
                'metadata' => [
                    'link' => 'https://example.com/seo-marketing',
                    'category_ids' => [3, 4],
                ],
            ],
        ];
    }
}
