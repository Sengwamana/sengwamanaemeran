<?php

namespace Database\Seeders\Themes\Main;

use Botble\Blog\Database\Traits\HasBlogSeeder;
use Botble\Theme\Database\Seeders\ThemeSeeder;
use Illuminate\Support\Facades\File;

class BlogSeeder extends ThemeSeeder
{
    use HasBlogSeeder;

    public function run(): void
    {
        $this->uploadFiles('posts');

        $this->createBlogCategories(array_map(fn ($category) => ['name' => $category], [
            'Web Development',
            'Open Source Contributions',
            'Tutorials',
            'Technology Reviews',
            'Personal Blog',
            'Career Journey',
            'Coding Challenges',
            'Design Portfolio',
            'Collaborations',
        ]));

        $this->createBlogTags(array_map(fn ($tag) => ['name' => $tag], [
            'Botble CMS',
            'JavaScript',
            'Open Source',
            'Web Design',
            'API Development',
            'Full Stack Development',
            'Vietnam Developer',
            'GitHub Projects',
        ]));

        $content = File::get(database_path('seeders/contents/post.html'));

        $posts = collect([
            'Building a Full-Stack App with the TALL Stack' => 'Learn how to build a full-stack web application using the TALL stack, integrating Laravel, Alpine.js, Tailwind CSS, and Livewire for dynamic and responsive web development.',
            'My Journey in Open Source: 3 Years of Contributions' => 'A personal reflection on my experiences contributing to open source projects over the past three years, sharing lessons learned and advice for aspiring contributors.',
            '5 Essential Tools for Web Developers in 2024' => 'Discover the top 5 tools that are essential for web developers in 2024, from frameworks and libraries to productivity boosters.',
            'How I Built My Personal Portfolio Using Botble CMS' => 'A detailed walkthrough on how I built my portfolio website using Botble CMS, customizing it with the Zelio template for an impressive online presence.',
            'Creating Responsive UIs with Tailwind CSS' => 'Learn how to create responsive user interfaces quickly and efficiently using the utility-first CSS framework, Tailwind CSS.',
            'Why I Love Contributing to Open Source Projects' => 'A deep dive into why open source matters to me, how it helped me grow as a developer, and why every developer should contribute to open source.',
            'A Deep Dive into Laravel for Beginners' => 'A comprehensive guide for beginners who want to learn Laravel, covering everything from installation to building a simple application.',
            'Exploring the Benefits of MySQL for Large-Scale Projects' => 'An exploration of why MySQL is a great choice for large-scale projects, highlighting features, scalability, and performance tips.',
            'How to Integrate APIs in Node.js for Your Next Project' => 'Learn how to seamlessly integrate third-party APIs in your Node.js applications for powerful data access and functionality.',
            'Best Practices for Designing User-Friendly Websites' => 'Discover the best practices for designing websites that are not only aesthetically pleasing but also user-friendly and accessible.',
            'Lessons from My First Web Development Job' => 'Reflecting on my first web development job, the lessons I learned, the challenges I faced, and how it shaped my career.',
            'How to Contribute to Open Source: A Beginner’s Guide' => 'A step-by-step guide on how beginners can start contributing to open source projects, with tips on finding the right project and making meaningful contributions.',
            'Optimizing Web Performance with React.js' => 'Learn how to optimize your React.js applications for better performance, focusing on code splitting, lazy loading, and efficient state management.',
            'My Top 5 GitHub Projects' => 'An overview of my top 5 GitHub projects, showcasing what I’ve built and how they’ve helped me grow as a developer.',
            'Adapting to the New Web Development Trends in 2024' => 'A look at the latest trends in web development for 2024, including new technologies, best practices, and what the future holds for developers.',
        ]);

        $this->createBlogPosts($posts->map(function ($description, $title) use ($content) {
            return [
                'name' => $title,
                'description' => $description,
                'content' => $content,
                'image' => $this->filePath(sprintf('posts/%s.png', $this->faker->numberBetween(1, 12))),
                'created_at' => $this->faker->dateTimeBetween('-1 year'),
            ];
        })->all());
    }
}
