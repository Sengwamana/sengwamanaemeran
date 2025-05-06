<?php

namespace Database\Seeders\Themes\Main;

use Botble\Menu\Database\Traits\HasMenuSeeder;
use Botble\Page\Database\Traits\HasPageSeeder;
use Botble\Page\Models\Page;
use Botble\Theme\Database\Seeders\ThemeSeeder;

class MenuSeeder extends ThemeSeeder
{
    use HasMenuSeeder;
    use HasPageSeeder;

    public function run(): void
    {
        $this->createMenus([
            [
                'name' => 'Main Menu',
                'location' => 'main-menu',
                'items' => [
                    [
                        'title' => 'Home',
                        'reference_type' => Page::class,
                        'reference_id' => $this->getPageId('Home'),
                        'children' => [
                            [
                                'title' => 'Home 1 - Designer',
                                'url' => 'https://zelio.botble.com',
                                'target' => '_blank',
                            ],
                            [
                                'title' => 'Home 2 - Developer',
                                'url' => 'https://zelio-developer.botble.com',
                                'target' => '_blank',
                            ],
                            [
                                'title' => 'Home 3 - Writer',
                                'url' => 'https://zelio-writer.botble.com',
                                'target' => '_blank',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Services',
                        'reference_type' => Page::class,
                        'reference_id' => $this->getPageId('Services'),
                    ],
                    [
                        'title' => 'Portfolio',
                        'reference_type' => Page::class,
                        'reference_id' => $this->getPageId('Projects'),
                    ],
                    [
                        'title' => 'Pricing',
                        'reference_type' => Page::class,
                        'reference_id' => $this->getPageId('Pricing'),
                    ],
                    [
                        'title' => 'Blog',
                        'reference_type' => Page::class,
                        'reference_id' => $this->getPageId('Blog'),
                    ],
                    [
                        'title' => 'Contact',
                        'reference_type' => Page::class,
                        'reference_id' => $this->getPageId('Contact'),
                    ],
                ],
            ],
        ]);
    }
}
