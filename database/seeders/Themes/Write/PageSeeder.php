<?php

namespace Database\Seeders\Themes\Write;

use Botble\Portfolio\Models\Project;
use Botble\Portfolio\Models\Service;
use Botble\Testimonial\Models\Testimonial;
use Database\Seeders\Themes\Main\PageSeeder as MainPageSeeder;

class PageSeeder extends MainPageSeeder
{
    protected int $style = 3;

    public function getData(): array
    {
        $this->uploadFiles('education');

        return [
            [
                'name' => 'Home',
                'content' => $this->generateShortcodeContent([
                    [
                        'name' => 'hero-banner',
                        'attributes' => [
                            'style' => 3,
                            'title' => 'Crafting Stories <span class=\'text-dark\'>with Passion: Discover the Work</span> of Meisa',
                            'subtitle' => 'Shaping Narratives, Igniting Minds',
                            'description' => 'Welcome to the creative world of Meisa Rosie, where words are crafted into captivating stories and insightful content. Explore her journey as an award-winning writer and discover how she brings imagination to life through her unique voice and compelling narratives.',
                            'primary_button_text' => 'Download My CV',
                            'primary_button_link' => '/storage/main/resume/cv.pdf',
                            'primary_button_icon' => 'ti ti-download',
                            'secondary_button_text' => 'Hire Me',
                            'secondary_button_link' => '#contact',
                            'secondary_button_icon' => 'ti ti-arrow-right',
                            'open_secondary_link_in_the_new_tab' => 'no',
                            'enable_lazy_loading' => 'no',
                        ],
                    ],
                    [
                        'name' => 'projects',
                        'attributes' => [
                            'style' => 3,
                            'title' => 'Typical Works',
                            'project_ids' => Project::query()->take(3)->pluck('id')->implode(','),
                            'enable_lazy_loading' => 'no',
                        ],
                    ],
                    [
                        'name' => 'services',
                        'attributes' => [
                            'style' => 3,
                            'title' => 'My Services',
                            'service_ids' => Service::query()->take(4)->pluck('id')->implode(','),
                            'enable_lazy_loading' => 'yes',
                        ],
                    ],
                    [
                        'name' => 'resume',
                        'attributes' => [
                            'style' => 3,
                            'resume_1_title' => 'Education',
                            'resume_1_display_type' => 'default',
                            'resume_1_quantity' => 3,
                            'resume_1_time_1' => '2023 - 2024',
                            'resume_1_title_1' => 'Certificates in Digital Content and SEO Writing',
                            'resume_1_subtitle_1' => 'CM Institute',
                            'resume_1_image_1' => $this->filePath('education/1.png'),
                            'resume_1_time_2' => '2019 - 2021',
                            'resume_1_title_2' => 'Writing Workshops and Continuing Education',
                            'resume_1_subtitle_2' => 'University of Iowa',
                            'resume_1_image_2' => $this->filePath('education/2.png'),
                            'resume_1_time_3' => '2016 - 2018',
                            'resume_1_title_3' => 'Bachelor of Arts in English Literature',
                            'resume_1_subtitle_3' => 'Bachelor of Arts',
                            'resume_1_image_3' => $this->filePath('education/3.png'),
                            'resume_2_title' => 'Awards',
                            'resume_2_display_type' => 'timeline',
                            'resume_2_quantity' => 3,
                            'resume_2_time_1' => '2018 - 2020',
                            'resume_2_title_1' => 'Columnist for The New Yorker',
                            'resume_2_subtitle_1' => 'NY Times',
                            'resume_2_description_1' => 'Worked with various clients, including magazines, websites, and publishing houses, to produce high-quality content across multiple genres.',
                            'resume_2_time_2' => '2016 - 2018',
                            'resume_2_title_2' => 'Content Writer at GitHub',
                            'resume_2_subtitle_2' => 'GitHub',
                            'resume_2_description_2' => 'Created engaging articles, blog posts, and features for one of the leading literary websites.',
                            'resume_2_time_3' => '2014 - 2016',
                            'resume_2_title_3' => 'Editor at The Write Stuff',
                            'resume_2_subtitle_3' => 'A.Lecturer',
                            'resume_2_description_3' => 'Overseeing the editorial process, providing guidance, and ensuring the highest standards of content.',
                        ],
                    ],
                    [
                        'name' => 'blog-posts',
                        'attributes' => [
                            'style' => 3,
                            'title' => 'From Blog',
                            'enable_lazy_loading' => 'yes',
                        ],
                    ],
                    [
                        'name' => 'testimonials',
                        'attributes' => [
                            'style' => 2,
                            'title' => 'Testimonials',
                            'testimonial_ids' => Testimonial::query()->pluck('id')->implode(','),
                            'enable_lazy_loading' => 'yes',
                        ],
                    ],
                    $this->getContactFormShortcode(),
                ]),
                'template' => 'sidebar',
            ],
        ];
    }
}
