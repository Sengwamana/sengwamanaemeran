<?php

namespace Database\Seeders\Themes\Code;

use Database\Seeders\Themes\Main\DatabaseSeeder as MainDatabaseSeeder;

class DatabaseSeeder extends MainDatabaseSeeder
{
    public function getData(): array
    {
        return [
            ...parent::getData(),
            PortfolioSeeder::class,
            PageSeeder::class,
            WidgetSeeder::class,
            ThemeOptionSeeder::class,
        ];
    }
}
