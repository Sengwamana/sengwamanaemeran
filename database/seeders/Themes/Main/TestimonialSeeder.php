<?php

namespace Database\Seeders\Themes\Main;

use Botble\Testimonial\Models\Testimonial;
use Botble\Theme\Database\Seeders\ThemeSeeder;

class TestimonialSeeder extends ThemeSeeder
{
    public function run(): void
    {
        $this->uploadFiles('avatars', 'main');

        Testimonial::query()->truncate();

        foreach ($this->getData() as $item) {
            Testimonial::query()->create([
                ...$item,
                'content' => '“' . $item['content'] . '”',
                'image' => $this->filePath("avatars/{$this->fake()->randomElement([1, 2])}.png"),
            ]);
        }
    }

    public function getData(): array
    {
        return [
            [
                'name' => 'Jennifer Lee',
                'company' => 'Happy Home Seeker',
                'content' => 'Working with this team was an absolute pleasure. Their attention to detail, professionalism, and understanding of my needs made the entire home buying process stress-free and enjoyable.',
            ],
            [
                'name' => 'Robert Evans',
                'company' => 'Property Investor',
                'content' => 'The guidance and insights provided by this team were invaluable in helping me secure profitable investments. Their market knowledge and dedication to client success are unmatched.',
            ],
            [
                'name' => 'Jessica White',
                'company' => 'Delighted Home Seller',
                'content' => 'I couldn’t have asked for a smoother selling experience. Their expert advice, flawless staging, and negotiation skills resulted in a quick sale at a great price. Truly impressive!',
            ],
            [
                'name' => 'Daniel Miller',
                'company' => 'Happy New Homeowner',
                'content' => 'From start to finish, the home buying experience was handled with care and professionalism. The team listened to all my concerns and helped me find the perfect home in no time.',
            ],
        ];
    }
}
