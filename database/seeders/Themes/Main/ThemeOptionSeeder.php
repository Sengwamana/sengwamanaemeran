<?php

namespace Database\Seeders\Themes\Main;

use Botble\Page\Database\Traits\HasPageSeeder;
use Botble\Theme\Database\Seeders\ThemeSeeder;
use Botble\Theme\Database\Traits\HasThemeOptionSeeder;
use Botble\Theme\Supports\ThemeSupport;

class ThemeOptionSeeder extends ThemeSeeder
{
    use HasPageSeeder;
    use HasThemeOptionSeeder;

    public function run(): void
    {
        $this->createThemeOptions($this->getData());
    }

    public function getData(): array
    {
        return [
            'favicon' => $this->filePath('general/favicon.png'),
            'logo' => $this->filePath('general/favicon.png'),
            'logo_dark' => $this->filePath('general/logo-dark.png'),
            'site_title' => 'Showcasing Creative Designs and Innovative Projects',
            'site_name' => 'william.design',
            'seo_description' => 'Discover innovative designs, creative projects, and unique artistic works. Showcasing the expertise and vision behind every creation.',
            'tp_primary_font' => 'Urbanist',
            'primary_color' => '#6e4ef2',
            'gradient_color' => '#8c71ff',
            'homepage_id' => $this->getPageId('Home'),
            'social_links' => ThemeSupport::getDefaultSocialLinksData(),
            'copyright' => '© %Y All Rights Reserved by <a href="https://botble.com" class="text-primary">botble.com</a>.',
            'preloader_enabled' => 'yes',
            'preloader_version' => 'v2',
            'footer_background' => $this->filePath('general/footer-bg.png'),
            'header_style' => 1,
            'footer_style' => 1,
            'preloader_style' => 1,
            'blog_page_id' => $this->getPageId('Blog'),
            'post_item_style' => 1,
            'post_item_per_row' => 3,
            'cookie_consent_learn_more_url' => '/cookie-policy',
            'cookie_consent_learn_more_text' => 'Cookie Policy',
        ];
    }
}
