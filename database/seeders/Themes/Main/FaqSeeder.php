<?php

namespace Database\Seeders\Themes\Main;

use Botble\Base\Supports\BaseSeeder;
use Botble\Faq\Models\Faq;
use Botble\Faq\Models\FaqCategory;

class FaqSeeder extends BaseSeeder
{
    public function run(): void
    {
        Faq::query()->truncate();
        FaqCategory::query()->truncate();

        $categories = [
            'Service Offerings',
            'Cost and Billing',
            'Follow-Up Support',
        ];

        foreach ($categories as $index => $category) {
            FaqCategory::query()->create([
                'name' => $category,
                'order' => $index,
            ]);
        }

        $faqs = [
            [
                'question' => 'What is business consulting?',
                'answer' => 'Business consulting involves providing expert advice to organizations to help them improve their performance and achieve their business objectives.',
            ],
            [
                'question' => 'How can consulting services benefit my business?',
                'answer' => 'Consulting services can provide insights, strategies, and solutions to address specific challenges, improve efficiency, enhance decision-making, and ultimately contribute to the overall success of your business.',
            ],
            [
                'question' => 'What specific services do you provide?',
                'answer' => 'We offer a range of services, including strategic planning, financial advisory, operations optimization, market research, and more. Our goal is to tailor our services to meet the unique needs of each client.',
            ],
            [
                'question' => 'How do you structure your fees?',
                'answer' => 'Our fees are structured based on the scope and complexity of the project. We offer different pricing models, including hourly rates, project-based fees, and retainer agreements. The specific fee structure will be discussed and agreed upon during the initial consultation.',
            ],
            [
                'question' => 'What industries do you specialize in?',
                'answer' => 'We have experience and expertise across various industries, including but not limited to technology, finance, healthcare, and manufacturing. Our consultants work closely with clients to understand industry-specific challenges and provide tailored solutions.',
            ],
            [
                'question' => 'Can you share any client testimonials or case studies?',
                'answer' => 'Certainly! We have a collection of client testimonials and case studies that highlight the success stories of our consulting engagements. Please visit our \'Client Success Stories\' section for more details.',
            ],
            [
                'question' => 'How do you collaborate with clients during the consulting process?',
                'answer' => 'We believe in a collaborative approach. Throughout the consulting process, we maintain open communication with our clients, involve key stakeholders, and ensure that the client\'s perspective is integral to the decision-making process.',
            ],
            [
                'question' => 'How long does a typical consulting engagement last?',
                'answer' => 'The duration of a consulting engagement varies depending on the nature and scope of the project. During the initial consultation, we work with clients to define the timeline and milestones for the project, ensuring alignment with their goals and objectives.',
            ],
            [
                'question' => 'Who are the key members of your consulting team?',
                'answer' => 'Our consulting team consists of highly qualified and experienced professionals with diverse backgrounds. You can learn more about our team members on the \'Meet the Team\' page of our website.',
            ],
            [
                'question' => 'How do you handle client information and sensitive data?',
                'answer' => 'We take data privacy and confidentiality seriously. Our firm adheres to strict security protocols to protect client information. We have established measures to ensure the confidentiality and security of sensitive data throughout the consulting process.',
            ],
            [
                'question' => 'Is there ongoing support after the consulting engagement?',
                'answer' => 'Yes, we provide ongoing support to our clients even after the completion of the consulting engagement. This may include follow-up meetings, additional training, and assistance with the implementation of recommended strategies to ensure sustained success.',
            ],
            [
                'question' => 'What is your policy regarding cancellations?',
                'answer' => 'Our cancellation policy is outlined in the consulting agreement. Generally, we require advance notice for cancellations, and any associated fees or refunds will be discussed and agreed upon during the initial engagement negotiations.',
            ],
            [
                'question' => 'What is your approach to sustainability consulting?',
                'answer' => 'In sustainability consulting, we work with clients to develop environmentally responsible and socially conscious business practices. Our approach involves assessing current operations, identifying areas for improvement, and implementing sustainable strategies to reduce environmental impact and promote corporate social responsibility.',
            ],
            [
                'question' => 'Do you offer remote consulting services?',
                'answer' => 'Yes, we offer remote consulting services to clients worldwide. Our team is equipped to conduct virtual meetings, collaborate online, and deliver effective consulting services remotely. This allows us to work with clients regardless of geographical location.',
            ],
            [
                'question' => 'How can your technology integration services benefit my business?',
                'answer' => 'Our technology integration services focus on streamlining business processes through the effective use of technology. By integrating the right technologies, we help businesses enhance efficiency, improve communication, and stay competitive in today\'s rapidly evolving digital landscape.',
            ],
            [
                'question' => 'What sets your leadership development programs apart?',
                'answer' => 'Our leadership development programs are designed to empower individuals at all levels of an organization. We go beyond traditional training, focusing on personalized coaching, mentorship, and experiential learning to cultivate effective and inspiring leaders within your company.',
            ],
            [
                'question' => 'How do you stay updated on industry trends and best practices?',
                'answer' => 'We are committed to staying at the forefront of industry trends and best practices. Our team actively engages in continuous learning, participates in relevant conferences, and maintains a strong network of industry professionals to ensure that our consulting services are informed by the latest insights and innovations.',
            ],
            [
                'question' => 'What measures do you take to ensure the quality of your consulting services?',
                'answer' => 'We prioritize the quality of our consulting services by implementing rigorous quality assurance processes. This includes regular reviews of our methodologies, ongoing training for our consultants, and soliciting feedback from clients to continuously improve our service delivery.',
            ],
        ];

        $categoryIds = FaqCategory::query()->pluck('id');

        foreach ($faqs as $faq) {
            Faq::query()->create([
                ...$faq,
                'category_id' => $categoryIds->random(),
            ]);
        }
    }
}
