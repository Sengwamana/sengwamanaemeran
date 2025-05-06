<?php

return [
    'name' => 'core/acl::auth.settings.email.title',
    'description' => 'core/acl::auth.settings.email.description',
    'templates' => [
        'password-reminder' => [
            'title' => 'core/acl::auth.settings.email.templates.password_reminder.title',
            'description' => 'core/acl::auth.settings.email.templates.password_reminder.description',
            'subject' => 'core/acl::auth.settings.email.templates.password_reminder.subject',
            'can_off' => false,
            'variables' => [
                'reset_link' => 'core/acl::auth.settings.email.templates.password_reminder.reset_link',
            ],
        ],
    ],
];
