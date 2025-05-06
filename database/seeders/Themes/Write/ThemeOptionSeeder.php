<?php

namespace Database\Seeders\Themes\Write;

use Botble\Theme\Supports\ThemeSupport;
use Database\Seeders\Themes\Main\ThemeOptionSeeder as MainThemeOptionSeeder;

class ThemeOptionSeeder extends MainThemeOptionSeeder
{
    public function getData(): array
    {
        return [
            'favicon' => $this->filePath('general/favicon.png'),
            'logo' => $this->filePath('general/favicon.png'),
            'seo_description' => 'Discover innovative designs, creative projects, and unique artistic works. Showcasing the expertise and vision behind every creation.',
            'homepage_id' => $this->getPageId('Home'),
            'social_links' => ThemeSupport::getDefaultSocialLinksData(),
            'copyright' => '© %Y All Rights Reserved by <a href="https://botble.com" class="text-primary">botble.com</a>.',
            'site_name' => 'Meisa',
            'site_title' => 'Professional Content Creator',
            'primary_color' => '#fcc6e2',
            'gradient_color' => '#40363b',
            'tp_primary_font' => 'Playfair Display',
            'tp_secondary_font' => 'Urbanist',
            'header_style' => 3,
            'footer_style' => 3,
            'preloader_style' => 3,
            'blog_page_id' => $this->getPageId('Blog'),
            'post_item_style' => 3,
            'post_item_per_row' => 1,
        ];
    }
}
