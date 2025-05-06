<?php

return [
    'name' => 'Portfolio',
    'settings' => [
        'success_message' => 'Settings has been saved successfully!',
        'email' => [
            'title' => 'Quotation Requests',
            'description' => 'Email templates for quotation requests',
            'templates' => [
                'notice_title' => 'Send notice to administrator',
                'notice_description' => 'Send notice to administrator when a quotation request is sent',
            ],
        ],
    ],
    'service_category' => [
        'name' => 'Service Categories',
        'create' => 'Create Service Category',
    ],
    'service' => [
        'name' => 'Services',
        'create' => 'Create Service',
    ],
    'package' => [
        'name' => 'Packages',
        'create' => 'Create Package',
    ],
    'project' => [
        'name' => 'Projects',
        'create' => 'Create Project',
        'author' => 'Author',
        'client' => 'Client',
        'place' => 'Place',
        'start_date' => 'Start Date',
    ],
    'quotation_request' => [
        'name' => 'Quotation Requests',
        'viewing' => 'Viewing quotation request #:name',
        'information' => 'Information',
    ],
    'custom_field' => [
        'name' => 'Custom Fields',
        'create' => 'Create',
        'type' => 'Type',
        'options' => 'Options',
        'placeholder' => 'Placeholder',
        'placeholder_placeholder' => 'Enter placeholder',
        'required' => 'Required',
        'option' => [
            'label' => 'Label',
            'value' => 'Value',
            'add_row' => 'Add new row',
            'add_from_global' => 'Add global custom field',
        ],
        'modal' => [
            'heading' => 'Add new custom field',
            'select_field' => 'Select field',
            'button' => 'Add new',
        ],
        'enums' => [
            'fields' => [
                'text' => 'Text',
                'number' => 'Number',
                'dropdown' => 'Dropdown',
                'checkbox' => 'Checkbox',
                'textarea' => 'Textarea',
            ],
        ],
        'ask_for_select' => 'Please select a custom field',
        'add_a_new_custom_field' => 'Add a new custom field',
    ],
    'image' => 'Image',
    'category' => 'Category',
    'duration' => 'Duration',
    'price' => 'Price',
    'annual_price' => 'Annual price',
    'form' => [
        'none' => 'None',
        'images' => 'Images',
        'name_placeholder' => 'Enter name',
        'is_featured' => 'Is featured?',
        'price_placeholder' => 'Enter price',
        'features' => 'Features',
        'features_help_block' => 'Separate by new line (+ is included, - is not included)',
        'features_placeholder' => 'Example:
+ 15-Days Shipping World Wide
+ Free Bubble Warp
- 24/7 Support
        ',
        'description_placeholder' => 'Enter description',
        'packages_switch_pricing_plan' => 'Enter annual price to switch pricing plan feature',
        'action_url' => 'Action URL',
        'action_label' => 'Action Label',
    ],
    'is_popular' => 'Is popular?',
    'enums' => [
        'package_durations' => [
            'hourly' => 'Hourly',
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'annually' => 'Annually',
            'quarterly' => 'Quarterly',
            'one_time' => 'One Time',
        ],
        'quote_statuses' => [
            'read' => 'Read',
            'unread' => 'Unread',
        ],
    ],
    'edit_this_service' => 'Edit this service',
    'edit_this_service_category' => 'Edit this service category',
    'edit_this_project' => 'Edit this project',
    'edit_this_project_category' => 'Edit this project category',
    'edit_this_package' => 'Edit this package',
    'message' => 'Message',
    'time' => 'Time',
    'service_categories' => 'Service Categories',
    'services' => 'Services',
    'projects' => 'Projects',
    'packages' => 'Packages',
];
