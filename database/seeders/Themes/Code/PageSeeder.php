<?php

namespace Database\Seeders\Themes\Code;

use Botble\Portfolio\Models\Project;
use Botble\Portfolio\Models\Service;
use Database\Seeders\Themes\Main\PageSeeder as MainPageSeeder;

class PageSeeder extends MainPageSeeder
{
    protected int $style = 2;

    public function getData(): array
    {
        $this->uploadFiles('general');
        $this->uploadFiles('skills');
        $this->uploadFiles('companies');
        $this->uploadFiles('experiences');

        return [
            [
                'name' => 'Home',
                'content' => $this->generateShortcodeContent([
                    [
                        'name' => 'hero-banner',
                        'attributes' => [
                            'style' => 2,
                            'title' => 'Senior <span>{Full Stack}</span>Web & App developer',
                            'subtitle' => 'Hey, I’m James',
                            'description' => 'With expertise in cutting-edge technologies such as <span>NodeJS</span>, <span>React</span>, <span>Angular</span>, and <span>Laravel</span>... I deliver web solutions that are both innovative and robust.',
                            'primary_button_text' => 'Download My CV',
                            'primary_button_link' => '/storage/main/resume/cv.pdf',
                            'primary_button_icon' => 'ti ti-download',
                            'below_button_text' => '...and more',
                            'open_secondary_link_in_the_new_tab' => 'no',
                            'quantity' => 5,
                            ...collect(['Next.js', 'Firebase', 'MongoDB', 'Node.js', 'Tailwind CSS'])
                                ->mapWithKeys(function ($name, $index) {
                                    $index++;

                                    return [
                                        "name_$index" => $name,
                                        "image_$index" => $this->filePath("skills/$index.png"),
                                    ];
                                })
                                ->all(),
                            'background_image' => $this->filePath('general/hero-bg.png'),
                            'background_image_dark' => $this->filePath('general/hero-bg-dark.png'),
                            'right_image' => $this->filePath('general/people.png'),
                            'right_image_shape' => $this->filePath('general/people-shape.png'),
                            'enable_lazy_loading' => 'no',
                        ],
                    ],
                    [
                        'name' => 'stats-counter',
                        'attributes' => [
                            'style' => 2,
                            'quantity' => 4,
                            'label_1' => 'Years Experience',
                            'icon_1' => 'ti ti-crown',
                            'count_1' => 12,
                            'label_2' => 'Projects Completed',
                            'icon_2' => 'ti ti-device-desktop',
                            'count_2' => 250,
                            'label_3' => 'Satisfied Clients',
                            'icon_3' => 'ti ti-heart-spark',
                            'count_3' => 680,
                            'label_4' => 'Awards Winner',
                            'icon_4' => 'ti ti-award',
                            'count_4' => 18,
                            'background_image' => $this->filePath('general/static-bg.png', 'code'),
                            'enable_lazy_loading' => 'no',
                        ],
                    ],
                    [
                        'name' => 'corporation',
                        'attributes' => [
                            'title' => 'More than +168 <span>companies <br /></span> trusted <span>worldwide_</span>',
                            'subtitle' => 'Cooperation',
                            'companies_quantity' => 9,
                            ...collect(['bravado', 'gocardless', 'google', 'intercom', 'monzo', 'samsung', 'spotify', 'stripe', 'visa'])
                                ->mapWithKeys(function ($name, $index) {
                                    $index++;

                                    return [
                                        "companies_name_$index" => $name,
                                        "companies_image_$index" => $this->filePath("companies/$name.png"),
                                    ];
                                })
                                ->all(),
                            'contact_avatar' => $this->filePath('general/corporation-avatar.png'),
                            'journey_title' => 'Git Journaling',
                            'journeys_quantity' => 5,
                            ...collect([
                                '15 Jul: Muzzilla-streaming-API-services-for-Python',
                                '30 Jun: ChatHub-Chat-application-VueJs-Mongodb',
                                '26 May: DineEasy-coffee-tea-reservation-system',
                                '17 Apr: FinanceBuddy-Personal-finance-tracker',
                                '05 Mar: TuneStream-Music-streaming-service-API',
                            ])
                                ->mapWithKeys(function ($item, $index) {
                                    $index++;

                                    [$date, $title] = explode(': ', $item);

                                    return [
                                        "journeys_date_$index" => $date,
                                        "journeys_title_$index" => $title,
                                    ];
                                })
                                ->all(),
                            'contacts_quantity' => 3,
                            ...collect([
                                ['label' => 'skype', 'content' => 'james.doe', 'icon' => 'ti ti-brand-skype', 'url' => 'skype:james.doe?chat'],
                                ['label' => 'email', 'content' => 'contact@botble.com', 'icon' => 'ti ti-mail', 'url' => 'mailto:contact@botble.com'],
                                ['label' => 'phone', 'content' => '+1-234-567-8901', 'icon' => 'ti ti-phone', 'url' => 'tel:+1-234-567-8901'],
                            ])
                                ->mapWithKeys(function ($item, $index) {
                                    $index++;

                                    return [
                                        "contacts_label_$index" => $item['label'],
                                        "contacts_content_$index" => $item['content'],
                                        "contacts_url_$index" => $item['url'],
                                        "contacts_icon_$index" => $item['icon'],
                                    ];
                                })
                                ->all(),
                            'enable_lazy_loading' => 'yes',
                        ],
                    ],
                    [
                        'name' => 'services',
                        'attributes' => [
                            'style' => 2,
                            'title' => 'Designing solutions <span class=\'text-300\'>customized<br>to meet your requirements</span>',
                            'subtitle' => 'Cooperation',
                            'service_ids' => Service::query()->pluck('id')->implode(','),
                            'bottom_text' => "Excited to take on <span class='text-dark'>new projects</span> and collaborate. <br>Let\'s chat about your ideas. <a href='' class='text-primary-2'>Reach out!</a>",
                            'enable_lazy_loading' => 'yes',
                        ],
                    ],
                    [
                        'name' => 'experience',
                        'attributes' => [
                            'title' => '+12 <span>years of</span> passion <span>for <br /> programming techniques</span>',
                            'subtitle' => 'Experience',
                            'role_title' => 'Senior Software Engineer',
                            'role_description' => 'Led development of scalable web applications, <span>improving performance</span> and user experience for millions of users. \n Implemented machine learning algorithms to enhance search functionality. \n Collaborated with cross-functional teams to integrate new features seamlessly.',
                            'experiences_quantity' => 4,
                            ...collect([
                                ['date' => '2018 - Present', 'title' => 'Google', 'logo' => 'google.png'],
                                ['date' => '2012 - 2015', 'title' => 'Twitter (X)', 'logo' => 'x.png'],
                                ['date' => '2018 - Present', 'title' => 'Amazon', 'logo' => 'amazon.png'],
                                ['date' => '2010 - 2012', 'title' => 'Paypal', 'logo' => 'paypal.png'],
                            ])
                                ->mapWithKeys(function ($item, $index) {
                                    $index++;

                                    return [
                                        "experiences_date_$index" => $item['date'],
                                        "experiences_title_$index" => $item['title'],
                                        "experiences_logo_$index" => $this->filePath("experiences/{$item['logo']}"),
                                    ];
                                })
                                ->all(),
                            'skills_quantity' => 5,
                            ...collect(['Python', 'TensorFlow', 'Angular', 'Kubernetes', 'GCP'])
                                ->mapWithKeys(function ($name, $index) {
                                    $index++;

                                    return [
                                        "skills_name_$index" => $name,
                                    ];
                                })
                                ->all(),
                            'background_image' => $this->filePath('general/services-bg.png'),
                            'enable_lazy_loading' => 'yes',
                        ],
                    ],
                    [
                        'name' => 'resume',
                        'attributes' => [
                            'style' => 2,
                            'resume_1_title' => 'Education',
                            'resume_1_title_icon' => 'ti ti-school',
                            'resume_1_quantity' => 4,
                            'resume_1_time_1' => '2018 - 2019',
                            'resume_1_title_1' => 'Certification in UX Design',
                            'resume_1_subtitle_1' => 'University of Stanford',
                            'resume_1_time_2' => '2017 - 2018',
                            'resume_1_title_2' => 'Certification in Web Dev',
                            'resume_1_subtitle_2' => 'University of Stanford',
                            'resume_1_time_3' => '2014 - 2016',
                            'resume_1_title_3' => 'Advanced UX/UI Bootcamp',
                            'resume_1_subtitle_3' => 'Design Academy',
                            'resume_1_time_4' => '2012 - 2013',
                            'resume_1_title_4' => 'Certification in Graphic Design',
                            'resume_1_subtitle_4' => 'Coursera',
                            'resume_2_title' => 'Experience',
                            'resume_2_title_icon' => 'ti ti-stars',
                            'resume_2_quantity' => 4,
                            'resume_2_time_1' => '2019 - Present',
                            'resume_2_title_1' => 'Senior UI/UX Designer',
                            'resume_2_subtitle_1' => 'Leader in Creative team',
                            'resume_2_time_2' => '2016 - 2019',
                            'resume_2_title_2' => 'UI/UX Designer',
                            'resume_2_subtitle_2' => 'Tech Startup',
                            'resume_2_time_3' => '2014 - 2016',
                            'resume_2_title_3' => 'Freelance UI/UX Designer',
                            'resume_2_subtitle_3' => 'Self-Employed',
                            'resume_2_time_4' => '2012 - 2014',
                            'resume_2_title_4' => 'Junior UI Designer',
                            'resume_2_subtitle_4' => 'Web Solutions team',
                            'enable_lazy_loading' => 'yes',
                        ],
                    ],
                    [
                        'name' => 'projects',
                        'attributes' => [
                            'style' => 2,
                            'title' => 'My Recent Works',
                            'subtitle' => 'Projects',
                            'project_ids' => Project::query()->pluck('id')->implode(','),
                            'background_image' => $this->filePath('general/projects-bg.png'),
                            'enable_lazy_loading' => 'yes',
                        ],
                    ],
                    [
                        'name' => 'skills',
                        'attributes' => [
                            'style' => 2,
                            'title' => 'My Skills',
                            'subtitle' => 'Projects',
                            'quantity' => 9,
                            ...collect([
                                'Next.js', 'Firebase', 'MongoDB', 'Node.js', 'Tailwind CSS', 'React', 'Vue.js', 'Angular', 'Laravel',
                            ])
                                ->mapWithKeys(function ($name, $index) {
                                    $index++;

                                    return [
                                        "name_$index" => $name,
                                        "image_$index" => $this->filePath("skills/$index.png"),
                                    ];
                                })
                                ->all(),
                            'list_quantity' => 5,
                            ...collect([
                                'Front-End: HTML, CSS, JavaScript, React, Angular',
                                'Back-End: Node.js, Express, Python, Django',
                                'Databases: MySQL, PostgreSQL, MongoDB',
                                'Tools & Platforms: Git, Docker, AWS, Heroku',
                                'Others: RESTful APIs, GraphQL, Agile Methodologies',
                            ])
                                ->mapWithKeys(function ($item, $index) {
                                    $index++;

                                    [$label, $content] = explode(': ', $item);

                                    return [
                                        "list_label_$index" => $label,
                                        "list_content_$index" => $content,
                                    ];
                                })
                                ->all(),
                            'enable_lazy_loading' => 'yes',
                        ],
                    ],
                    [
                        'name' => 'blog-posts',
                        'attributes' => [
                            'style' => 2,
                            'paginate' => 3,
                            'title' => 'Recent blog',
                            'subtitle' => 'Latest Posts',
                            'enable_lazy_loading' => 'yes',
                        ],
                    ],
                    $this->getContactFormShortcode(),
                ]),
            ],
        ];
    }
}
