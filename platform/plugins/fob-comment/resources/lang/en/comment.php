<?php

return [
    'common' => [
        'name' => 'Name',
        'email' => 'Email',
        'website' => 'Website',
        'comment' => 'Comment',
    ],

    'title' => 'Comments',
    'author' => 'Author',
    'responsed_to' => 'Response to',
    'permalink' => 'Permalink',
    'url' => 'URL',
    'submitted_on' => 'Submitted on',
    'edit_comment' => 'Edit Comment',
    'reply' => 'Reply',
    'in_reply_to' => 'In reply to :name',

    'reply_modal' => [
        'title' => 'Reply to :comment',
        'cancel' => 'Cancel',
    ],

    'allow_comments' => 'Allow comments',

    'front' => [
        'admin_badge' => 'Admin',

        'list' => [
            'title' => ':count comment|:count comments',
            'reply' => 'Reply',
            'reply_to' => 'Reply to :name',
            'cancel_reply' => 'Cancel reply',
            'waiting_for_approval_message' => 'Your comment is awaiting moderation. This is a preview, your comment will be visible after it has been approved.',
        ],

        'form' => [
            'title' => 'Leave a comment',
            'description' => 'Your email address will not be published. Required fields are marked *',
            'cookie_consent' => 'Save my name, email, and website in this browser for the next time I comment.',
            'submit' => 'Post Comment',
        ],

        'comment_success_message' => 'Your comment has been sent successfully.',
    ],

    'enums' => [
        'statuses' => [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'spam' => 'Spam',
            'trash' => 'Trash',
        ],
    ],

    'settings' => [
        'title' => 'FOB Comment',
        'description' => 'Configure settings for FOB Comment',

        'form' => [
            'enable_recaptcha' => 'Enable reCAPTCHA',
            'enable_recaptcha_help' => 'You need to enable reCAPTCHA in :url to use this feature.',
            'captcha_setting_label' => 'Captcha Settings',
            'comment_moderation' => 'Comments must be manually approved',
            'comment_moderation_help' => 'All comments must be manually approved by an admin before displaying on the frontend.',
            'show_comment_cookie_consent' => 'Show comments cookies checkbox, allowing visitors to save their information in the browser',
            'auto_fill_comment_form' => 'Auto-fill comment data for logged-in users',
            'auto_fill_comment_form_help' => 'The comment form will be automatically filled with user data such as full name, email, etc., if they are logged in.',
            'comment_order' => 'Sort comments by',
            'comment_order_help' => 'Choose the preferred order for displaying comments in the list.',
            'comment_order_choices' => [
                'asc' => 'Oldest',
                'desc' => 'Newest',
            ],
            'display_admin_badge' => 'Display admin badge for admin comments',
            'show_admin_role_name_for_admin_badge' => 'Show admin role name for admin badge',
            'show_admin_role_name_for_admin_badge_helper' => 'If enabled, the admin badge will display the admin role name instead of the default "Admin" text. If the admin role name is empty, the default text will be used. If user has multiple roles, the first role will be used.',
        ],
    ],
];
