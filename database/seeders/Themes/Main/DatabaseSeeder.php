<?php

namespace Database\Seeders\Themes\Main;

use Botble\ACL\Database\Seeders\UserSeeder;
use Botble\Language\Database\Seeders\LanguageSeeder;
use Botble\Theme\Database\Seeders\ThemeSeeder;
use FriendsOfBotble\Comment\Database\Seeders\CommentSeeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends ThemeSeeder
{
    public function run(): void
    {
        $this->prepareRun();

        $seeders = [];

        foreach ($this->getData() as $seeder) {
            $seeders[Str::afterLast($seeder, '\\')] = $seeder;
        }

        $this->call($seeders);

        $this->finished();
    }

    public function getData(): array
    {
        return [
            UserSeeder::class,
            LanguageSeeder::class,
            BlogSeeder::class,
            TestimonialSeeder::class,
            PortfolioSeeder::class,
            FaqSeeder::class,
            PageSeeder::class,
            MenuSeeder::class,
            WidgetSeeder::class,
            ThemeOptionSeeder::class,
            CommentSeeder::class,
        ];
    }
}
