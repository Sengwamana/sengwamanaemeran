<?php

namespace Database\Seeders\Themes\Code;

use Database\Seeders\Themes\Main\PortfolioSeeder as MainPortfolioSeeder;

class PortfolioSeeder extends MainPortfolioSeeder
{
    public function getCategories(): array
    {
        return [
            [
                'name' => 'Backend Development',
                'description' => 'Server-side development with robust, scalable architectures.',
            ],
            [
                'name' => 'Frontend Development',
                'description' => 'Building interactive and responsive web interfaces using modern technologies.',
            ],
            [
                'name' => 'Mobile App Development',
                'description' => 'Developing cross-platform mobile applications for Android and iOS.',
            ],
            [
                'name' => 'DevOps & Cloud',
                'description' => 'Cloud-based services and infrastructure management with CI/CD practices.',
            ],
        ];
    }

    public function getServices(): array
    {
        return [
            [
                'name' => 'API Development',
                'description' => 'Designing and developing scalable RESTful APIs for web and mobile applications.',
                'metadata' => [
                    'icon' => 'ti ti-api',
                ],
            ],
            [
                'name' => 'Frontend Development',
                'description' => 'Creating dynamic and interactive web pages using React, Vue, and other modern JS frameworks.',
                'metadata' => [
                    'icon' => 'ti ti-code',
                ],
            ],
            [
                'name' => 'Mobile App Development',
                'description' => 'Building cross-platform mobile applications using Flutter and React Native.',
                'metadata' => [
                    'icon' => 'ti ti-device-mobile',
                ],
            ],
            [
                'name' => 'DevOps Consulting',
                'description' => 'Implementing automated pipelines for continuous integration and delivery.',
                'metadata' => [
                    'icon' => 'ti ti-server',
                ],
            ],
            [
                'name' => 'Cloud Integration',
                'description' => 'Seamlessly integrating applications with cloud services like AWS, Azure, and Google Cloud.',
                'metadata' => [
                    'icon' => 'ti ti-cloud',
                ],
            ],
            [
                'name' => 'Database Management',
                'description' => 'Managing and optimizing relational and non-relational databases for high performance.',
                'metadata' => [
                    'icon' => 'ti ti-database',
                ],
            ],
        ];
    }

    public function getProjects(): array
    {
        return [
            [
                'name' => 'CRM System',
                'description' => 'A custom-built CRM system with real-time data synchronization and multi-user support.',
                'client' => 'Corporate Client',
                'start_date' => '2023-06-01',
                'metadata' => [
                    'link' => 'https://example.com/crm-system',
                    'github_url' => 'https://github.com/botble',
                    'category_ids' => [1],
                ],
            ],
            [
                'name' => 'E-Learning Platform',
                'description' => 'A scalable e-learning platform with integrated payment and live chat functionalities.',
                'client' => 'Education Startup',
                'start_date' => '2023-03-15',
                'metadata' => [
                    'link' => 'https://example.com/e-learning',
                    'github_url' => 'https://github.com/botble',
                    'category_ids' => [1, 2],
                ],
            ],
            [
                'name' => 'Mobile Banking App',
                'description' => 'A secure and user-friendly mobile banking app for managing personal finances.',
                'client' => 'Finance Company',
                'start_date' => '2022-09-10',
                'metadata' => [
                    'link' => 'https://example.com/mobile-banking',
                    'github_url' => 'https://github.com/botble',
                    'category_ids' => [3],
                ],
            ],
            [
                'name' => 'Cloud Infrastructure Setup',
                'description' => 'Set up a scalable and secure cloud infrastructure with automated monitoring and logging.',
                'client' => 'Tech Company',
                'start_date' => '2023-04-20',
                'metadata' => [
                    'link' => 'https://example.com/cloud-infrastructure',
                    'github_url' => 'https://github.com/botble',
                    'category_ids' => [4],
                ],
            ],
        ];
    }
}
