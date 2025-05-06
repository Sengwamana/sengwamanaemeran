<?php

return [
    'name' => 'plugins/contact::contact.settings.email.title',
    'description' => 'plugins/contact::contact.settings.email.description',
    'templates' => [
        'notice' => [
            'title' => 'plugins/contact::contact.settings.email.templates.notice_title',
            'description' => 'plugins/contact::contact.settings.email.templates.notice_description',
            'subject' => 'plugins/contact::contact.settings.email.templates.subject',
            'can_off' => true,
            'variables' => [
                'contact_name' => 'plugins/contact::contact.settings.email.templates.contact_name',
                'contact_subject' => 'plugins/contact::contact.settings.email.templates.contact_subject',
                'contact_email' => 'plugins/contact::contact.settings.email.templates.contact_email',
                'contact_phone' => 'plugins/contact::contact.settings.email.templates.contact_phone',
                'contact_address' => 'plugins/contact::contact.settings.email.templates.contact_address',
                'contact_content' => 'plugins/contact::contact.settings.email.templates.contact_content',
                'contact_custom_fields' => 'plugins/contact::contact.settings.email.templates.contact_custom_fields',
            ],
        ],

        'sender-confirmation' => [
            'title' => 'plugins/contact::contact.settings.email.templates.sender_confirmation_title',
            'description' => 'plugins/contact::contact.settings.email.templates.sender_confirmation_description',
            'subject' => 'plugins/contact::contact.settings.email.templates.sender_confirmation_subject',
            'can_off' => true,
            'enabled' => false,
            'variables' => [
                'contact_name' => 'plugins/contact::contact.settings.email.templates.contact_name',
                'contact_subject' => 'plugins/contact::contact.settings.email.templates.contact_subject',
                'contact_email' => 'plugins/contact::contact.settings.email.templates.contact_email',
                'contact_phone' => 'plugins/contact::contact.settings.email.templates.contact_phone',
                'contact_address' => 'plugins/contact::contact.settings.email.templates.contact_address',
                'contact_content' => 'plugins/contact::contact.settings.email.templates.contact_content',
                'contact_custom_fields' => 'plugins/contact::contact.settings.email.templates.contact_custom_fields',
            ],
        ],
    ],
];
