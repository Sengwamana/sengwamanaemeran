<?php

namespace Database\Seeders\Themes\Main;

use Botble\CookieConsent\Database\Traits\HasCookieConsentSeeder;
use Botble\Faq\Models\FaqCategory;
use Botble\Page\Database\Traits\HasPageSeeder;
use Botble\Portfolio\Models\Package;
use Botble\Portfolio\Models\Project;
use Botble\Portfolio\Models\Service;
use Botble\Testimonial\Models\Testimonial;
use Botble\Theme\Database\Seeders\ThemeSeeder;

class PageSeeder extends ThemeSeeder
{
    use HasCookieConsentSeeder;
    use HasPageSeeder;

    protected int $style = 1;

    public function run(): void
    {
        $this->truncatePages();

        $this->createPages([
            ...$this->getData(),
            [
                'name' => 'Services',
                'description' => '
                With expertise in mobile app and web design, I transform ideas into visually
                stunning and user-friendly interfaces that captivate and retain users.
                Explore my work and see design in action.',
                'content' => $this->generateShortcodeContent([
                    [
                        'name' => 'services',
                        'attributes' => [
                            'style' => $this->style !== 3 ? 4 : $this->style,
                            'enable_lazy_loading' => 'no',
                        ],
                    ],
                ]),
                'template' => 'has-heading',
            ],
            [
                'name' => 'Projects',
                'description' => '
                With expertise in mobile app and web design, I transform ideas into visually
                stunning and user-friendly interfaces that captivate and retain users.
                Explore my work and see design in action.',
                'content' => $this->generateShortcodeContent([
                    [
                        'name' => 'projects',
                        'attributes' => [
                            'style' => $this->style !== 3 ? 4 : $this->style,
                            'enable_lazy_loading' => 'no',
                        ],
                    ],
                ]),
                'template' => 'has-heading',
            ],
            [
                'name' => 'Pricing',
                'description' => 'Flexible Plans Tailored to Meet Your Unique Needs, Ensuring High-Quality Services
                Without Breaking the Bank',
                'content' => $this->generateShortcodeContent([
                    [
                        'name' => 'pricing-plans',
                        'attributes' => [
                            'package_ids' => Package::query()->pluck('id')->implode(','),
                            'enable_lazy_loading' => 'no',
                        ],
                    ],
                    [
                        'name' => 'faqs',
                        'attributes' => [
                            'title' => 'Common Questions',
                            'category_ids' => FaqCategory::query()->inRandomOrder()->value('id'),
                            'enable_lazy_loading' => 'no',
                        ],
                    ],
                ]),
                'template' => 'has-heading',
            ],
            [
                'name' => 'Blog',
                'description' => 'Discover key insights and emerging trends shaping the future of design: a detailed
                examination of how these innovations are reshaping our industry',
                'content' => '',
                'template' => 'has-heading',
            ],
            [
                'name' => 'Contact',
                'content' => $this->generateShortcodeContent([
                    $this->getContactFormShortcode(),
                ]),
                'template' => $this->style === 3 ? 'sidebar' : ($this->style === 2 ? 'has-heading' : 'default'),
            ],
            [
                'name' => $this->getCookieConsentPageName(),
                'content' => $this->getCookieConsentPageContent(),
                'template' => 'has-heading',
            ],
        ]);
    }

    public function getData(): array
    {
        $this->uploadFiles('general');
        $this->uploadFiles('brands', 'main');
        $this->uploadFiles('skills');
        $this->uploadFiles('resume', 'main');

        return [
            [
                'name' => 'Home',
                'content' => $this->generateShortcodeContent([
                    [
                        'name' => 'hero-banner',
                        'attributes' => [
                            'style' => 1,
                            'title' => 'Crafting Intuitive <span class=\'text-primary\'>Digital Experiences</span>',
                            'subtitle' => "👋 Hi there, I'm William",
                            'description' => 'I assist individuals and brands in achieving their objectives by creating and developing user-focused digital products and interactive experiences.',
                            'below_button_text' => '+ 12 years with professional design software',
                            'primary_button_text' => 'Download CV',
                            'primary_button_link' => '/storage/main/resume/cv.pdf',
                            'primary_button_icon' => 'ti ti-download',
                            'secondary_button_text' => 'Hire Me',
                            'secondary_button_link' => '#contact',
                            'secondary_button_icon' => 'ti ti-arrow-right',
                            'open_secondary_link_in_the_new_tab' => 'no',
                            'quantity' => 6,
                            ...collect(['Figma', 'Adobe XD', 'Illustrator', 'Sketch', 'Photoshop', 'Webflow'])
                                ->mapWithKeys(function ($name, $index) {
                                    $index++;

                                    return [
                                        "name_$index" => $name,
                                        "image_$index" => $this->filePath("skills/$index.png"),
                                    ];
                                })
                                ->all(),
                            'background_image' => $this->filePath('general/hero-bg.png'),
                            'right_image' => $this->filePath('general/hero-man.png'),
                            'right_image_shape' => $this->filePath('general/hero-decorate.png'),
                            'enable_lazy_loading' => 'no',
                        ],
                    ],
                    [
                        'name' => 'stats-counter',
                        'attributes' => [
                            'style' => 1,
                            'quantity' => 4,
                            'label_1' => 'Years of',
                            'highlight_text_1' => 'Experience',
                            'count_1' => 12,
                            'label_2' => 'Projects',
                            'highlight_text_2' => 'Completed',
                            'count_2' => 250,
                            'label_3' => 'Satisfied',
                            'highlight_text_3' => 'Happy Clients',
                            'count_3' => 680,
                            'label_4' => 'Awards',
                            'highlight_text_4' => 'Won Received',
                            'count_4' => 18,
                            'enable_lazy_loading' => 'no',
                        ],
                    ],
                    [
                        'name' => 'services',
                        'attributes' => [
                            'style' => 1,
                            'title' => 'What do I offer?',
                            'subtitle' => 'My journey started with a fascination for design and technology, leading me to specialize in UI/UX design',
                            'service_ids' => Service::query()->pluck('id')->implode(','),
                            'enable_lazy_loading' => 'yes',
                        ],
                    ],
                    [
                        'name' => 'projects',
                        'attributes' => [
                            'style' => 1,
                            'title' => 'My Latest Works',
                            'subtitle' => 'I believe that working hard and trying to learn every day will <br> make me improve in satisfying my customers.',
                            'project_ids' => Project::query()->pluck('id')->implode(','),
                            'bottom_action_text' => 'View all',
                            'bottom_action_link' => '/projects',
                            'enable_lazy_loading' => 'yes',
                        ],
                    ],
                    [
                        'name' => 'resume',
                        'attributes' => [
                            'style' => 1,
                            'title' => 'My Resume',
                            'subtitle' => 'I believe that working hard and trying to learn every day will <br> make me improve in satisfying my customers.',
                            'action_text' => 'Get in touch',
                            'action_link' => '#contact',
                            'resume_1_title' => 'Education',
                            'resume_1_title_icon' => 'ti ti-school',
                            'resume_1_quantity' => 4,
                            'resume_1_time_1' => '2018 - 2019',
                            'resume_1_title_1' => 'Certification in UX Design',
                            'resume_1_description_1' => 'University of Stanford',
                            'resume_1_subtitle_1' => '4.9/5',
                            'resume_1_time_2' => '2017 - 2018',
                            'resume_1_title_2' => 'Certification in Web Dev',
                            'resume_1_description_2' => 'University of Stanford',
                            'resume_1_subtitle_2' => '5.0/5',
                            'resume_1_time_3' => '2014 - 2016',
                            'resume_1_title_3' => 'Advanced UX/UI Bootcamp',
                            'resume_1_description_3' => 'Design Academy',
                            'resume_1_subtitle_3' => '4.9/5',
                            'resume_1_time_4' => '2012 - 2013',
                            'resume_1_title_4' => 'Certification in Graphic Design',
                            'resume_1_description_4' => 'Coursera',
                            'resume_1_subtitle_4' => '4.8/5',
                            'resume_2_title' => 'Experience',
                            'resume_2_title_icon' => 'ti ti-stars',
                            'resume_2_quantity' => 4,
                            'resume_2_time_1' => '2019 - Present',
                            'resume_2_title_1' => 'Senior UI/UX Designer',
                            'resume_2_description_1' => 'Leader in Creative team',
                            'resume_2_time_2' => '2016 - 2019',
                            'resume_2_title_2' => 'UI/UX Designer',
                            'resume_2_description_2' => 'Tech Startup',
                            'resume_2_time_3' => '2014 - 2016',
                            'resume_2_title_3' => 'Freelance UI/UX Designer',
                            'resume_2_description_3' => 'Self-Employed',
                            'resume_2_time_4' => '2012 - 2014',
                            'resume_2_title_4' => 'Junior UI Designer',
                            'resume_2_description_4' => 'Web Solutions team',
                            'bottom_text' => 'Branding . Marketing . User Interface',
                            'background_image' => $this->filePath('general/grid-bg.png'),
                            'enable_lazy_loading' => 'yes',
                        ],
                    ],
                    [
                        'name' => 'skills',
                        'attributes' => [
                            'style' => 1,
                            'title' => 'My Skills',
                            'subtitle' => 'I thrive on turning complex problems into simple, beautiful <br> solutions that enhance user satisfaction.',
                            'quantity' => 7,
                            ...collect([
                                'Figma', 'Adobe XD', 'Illustrator', 'Sketch', 'Photoshop', 'Webflow', 'Framer',
                            ])
                                ->mapWithKeys(function ($name, $index) {
                                    $index++;

                                    return [
                                        "name_$index" => $name,
                                        "image_$index" => $this->filePath("skills/$index.png"),
                                        "level_$index" => rand(70, 99) . '%',
                                    ];
                                })
                                ->all(),
                            'extra_content' => 'In addition, I have some programming knowledge such as: <br> <span class=\'fs-5 fw-bold text-dark\'>HTML, CSS, Javascript, Bootstrap, TailwindCSS</span>',
                            'enable_lazy_loading' => 'yes',
                        ],
                    ],
                    [
                        'name' => 'image-slider',
                        'attributes' => [
                            'title' => 'Trusted by industry leaders',
                            'subtitle' => 'I have collaborated with many large corporations, companies, and agencies around <br> the world in many fields of design and consulting',
                            'quantity' => 10,
                            ...collect([
                                'Brave', 'Circle', 'Discord', 'Google', 'jump_', 'Magic Eden', 'MetaMask', 'Shopify', 'Stripe', 'Lolliapaloza',
                            ])
                                ->mapWithKeys(function ($name, $index) {
                                    $index++;
                                    $url = mb_strtolower($name) === 'jump_' ? 'https://jump.com' : "https://{$name}.com";

                                    return [
                                        "name_$index" => $name,
                                        "image_$index" => $this->filePath("brands/$index.png"),
                                        "url_$index" => $url,
                                        "open_in_new_tab_$index" => true,
                                    ];
                                })
                                ->all(),
                            'enable_lazy_loading' => 'yes',
                        ],
                    ],
                    [
                        'name' => 'testimonials',
                        'attributes' => [
                            'title' => "Client's Testimonials",
                            'subtitle' => 'I believe that working hard and trying to learn every day will make me <br> improve in satisfying my customers.',
                            'testimonial_ids' => Testimonial::query()->pluck('id')->implode(','),
                            'shape_image' => $this->filePath('avatars/man.png'),
                            'enable_lazy_loading' => 'yes',
                        ],
                    ],
                    [
                        'name' => 'blog-posts',
                        'attributes' => [
                            'style' => 1,
                            'paginate' => 3,
                            'title' => 'Recent blog',
                            'subtitle' => 'Explore the insights and trends shaping our industry',
                            'action_text' => 'View more',
                            'action_link' => '/blog',
                            'enable_lazy_loading' => 'yes',
                        ],
                    ],
                    [
                        'name' => 'contact-form',
                        'attributes' => [
                            'style' => 1,
                            'display_fields' => 'phone,email,subject,address',
                            'mandatory_fields' => 'email,subject',
                            'title' => 'Get in touch',
                            'subtitle' => "I'm always excited to take on new projects and collaborate with innovative minds. If you <br> have a project in mind or just want to chat about design, feel free to reach out!",
                            'quantity' => 4,
                            'label_1' => 'Phone',
                            'content_1' => '+1-234-567-8901',
                            'icon_1' => 'ti ti-phone',
                            'url_1' => 'tel:+1-234-567-8901',
                            'label_2' => 'Email',
                            'content_2' => 'contact@botble.com',
                            'icon_2' => 'ti ti-mail',
                            'url_2' => 'mailto:contact@botble.com',
                            'label_3' => 'X (Twitter)',
                            'content_3' => 'Botble Technologies',
                            'icon_3' => 'ti ti-user',
                            'url_3' => 'https://x.com/botble',
                            'label_4' => 'Address',
                            'content_4' => '0811 Erdman Prairie, Joaville CA',
                            'icon_4' => 'ti ti-map',
                            'url_4' => 'https://google.com/maps',
                        ],
                    ],
                ]),
                'template' => 'default',
            ],
        ];
    }

    public function getContactFormShortcode(): array
    {
        return [
            'name' => 'contact-form',
            'attributes' => [
                'style' => $this->style,
                'display_fields' => 'phone,email,subject,address',
                'mandatory_fields' => 'email,subject',
                'title' => "Let's connect",
                'quantity' => 4,
                'label_1' => 'Phone',
                'content_1' => '+1-234-567-8901',
                'icon_1' => 'ti ti-phone',
                'url_1' => 'tel:+1-234-567-8901',
                'label_2' => 'Email',
                'content_2' => 'contact@botble.com',
                'icon_2' => 'ti ti-mail',
                'url_2' => 'mailto:contact@botble.com',
                'label_3' => 'X (Twitter)',
                'content_3' => 'Botble Technologies',
                'icon_3' => 'ti ti-user',
                'url_3' => 'https://x.com/botble',
                'label_4' => 'Address',
                'content_4' => '0811 Erdman Prairie, Joaville CA',
                'icon_4' => 'ti ti-map',
                'url_4' => 'https://google.com/maps',
            ],
        ];
    }
}
