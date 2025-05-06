<?php

return [
    'name' => 'plugins/portfolio::portfolio.settings.email.title',
    'description' => 'plugins/portfolio::portfolio.settings.email.description',
    'templates' => [
        'quote-request-notice' => [
            'title' => 'plugins/portfolio::portfolio.settings.email.templates.notice_title',
            'description' => 'plugins/portfolio::portfolio.settings.email.templates.notice_description',
            'subject' => 'Request for Quotation',
            'can_off' => true,
            'variables' => [
                'site_name' => 'Site name',
                'contact_name' => 'Contact name',
                'contact_email' => 'Contact email',
                'contact_message' => 'Contact message',
                'fields' => 'Custom fields',
            ],
        ],
    ],
];
