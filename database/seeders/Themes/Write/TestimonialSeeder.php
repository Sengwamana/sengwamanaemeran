<?php

namespace Database\Seeders\Themes\Write;

use Database\Seeders\Themes\Main\TestimonialSeeder as MainTestimonialSeeder;

class TestimonialSeeder extends MainTestimonialSeeder
{
    public function getData(): array
    {
        return [
            [
                'name' => 'John Doe',
                'company' => 'Author',
                'content' => "Meisa's writing is simply magical. She has the ability to capture the essence of a story and preset it in the most captivating way.",
            ],
            [
                'name' => 'Jane Smith',
                'company' => 'Editor',
                'content' => 'Working with Meisa was a fantastic experience. Her attention to detail and commitment to quality are unparalleled.',
            ],
            [
                'name' => 'David Brown',
                'company' => 'Publisher',
                'content' => 'Meisa is a true professional. Her writing is engaging and thought-provoking, and she always delivers on time.',
            ],
            [
                'name' => 'Emily White',
                'company' => 'Reader',
                'content' => 'I have been a fan of Meisa for years. Her stories are beautifully written and always leave me wanting more.',
            ],
        ];
    }
}
