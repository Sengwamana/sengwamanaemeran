<?php

namespace Database\Seeders\Themes\Write;

use Database\Seeders\Themes\Main\DatabaseSeeder as MainDatabaseSeeder;

class DatabaseSeeder extends MainDatabaseSeeder
{
    public function getData(): array
    {
        $this->uploadFiles('general');

        return [
            ...parent::getData(),
            PortfolioSeeder::class,
            TestimonialSeeder::class,
            PageSeeder::class,
            WidgetSeeder::class,
            ThemeOptionSeeder::class,
        ];
    }
}
