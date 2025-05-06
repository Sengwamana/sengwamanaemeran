<?php

namespace Database\Seeders\Themes\Code;

use Database\Seeders\Themes\Main\ThemeOptionSeeder as MainThemeOptionSeeder;

class ThemeOptionSeeder extends MainThemeOptionSeeder
{
    public function getData(): array
    {
        return [
            ...parent::getData(),
            'site_name' => 'James.dev',
            'site_title' => 'Web & App developer',
            'primary_color' => '#62a92b',
            'gradient_color' => '#659932',
            'tp_primary_font' => 'DM Mono',
            'logo_dark' => $this->filePath('general/favicon.png'),
            'header_style' => 2,
            'footer_style' => 2,
            'preloader_style' => 2,
            'blog_page_id' => $this->getPageId('Blog'),
            'post_item_style' => 2,
            'post_item_per_row' => 3,
        ];
    }
}
