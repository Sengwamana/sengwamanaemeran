<?php

namespace Database\Seeders\Themes\Write;

use Database\Seeders\Themes\Main\PortfolioSeeder as MainPortfolioSeeder;

class PortfolioSeeder extends MainPortfolioSeeder
{
    public function getCategories(): array
    {
        return [
            ['name' => 'Writing'],
            ['name' => 'Editing'],
            ['name' => 'SEO Writing'],
        ];
    }

    public function getServices(): array
    {
        return [
            [
                'name' => 'Writing: Novels, Short Stories, and Poetry',
                'description' => "Whether you're looking for an immersive novel, a captivating short story, or evocative poetry, I am your go-to writer. With a deep understanding of character development, plot structure, and lyrical prose, I craft stories that resonate and inspire.",
            ],
            [
                'name' => 'Articles, Blog Posts, and Website Content',
                'description' => "Today's digital age, engaging content is crucial for capturing your audience's attention. I specialize in creating high-quality content that aligns with your brand's voice and objectives.",
            ],
            [
                'name' => 'Precision Proofreading and Editing',
                'description' => 'Enhance your brand with compelling stories and memorable content that resonates with your audience.',
            ],
            [
                'name' => 'SEO-Optimized Writing Services',
                'description' => 'Improve your online visibility with our SEO-focused content, designed to rank higher and attract more visitors.',
            ],
        ];
    }

    public function getProjects(): array
    {
        $this->uploadFiles('projects');

        return [
            [
                'name' => 'Novels: Echoes of the Past',
                'description' => 'Set in a dystopian future, "Reflections of Tomorrow" is a thought-provoking novel that explores themes of identity, freedom, and resilience. Through its compelling storyline and well-drawn characters, the book invites readers to ponder the ethical dilemmas of technological advancement and societal change.',
                'client' => 'Corporate Client',
                'start_date' => '2023-06-01',
            ],
            [
                'name' => 'Whispers in the Wind',
                'description' => 'Set in a dystopian future, "Reflections of Tomorrow" is a thought-provoking novel that explores themes of identity, freedom, and resilience. Through its compelling storyline and well-drawn characters, the book invites readers to ponder the ethical dilemmas of technological advancement and societal change.',
                'client' => 'Education Startup',
                'start_date' => '2023-03-15',
            ],
            [
                'name' => 'Reflections of Tomorrow',
                'description' => 'Set in a dystopian future, "Reflections of Tomorrow" is a thought-provoking novel that explores themes of identity, freedom, and resilience. Through its compelling storyline and well-drawn characters, the book invites readers to ponder the ethical dilemmas of technological advancement and societal change.',
                'client' => 'Finance Company',
                'start_date' => '2022-09-10',
            ],
        ];
    }
}
