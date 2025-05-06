<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Database\Seeders\Themes\Main\DatabaseSeeder as MainDatabaseSeeder;

class DatabaseSeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->call(MainDatabaseSeeder::class);
    }
}
