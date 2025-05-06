<?php

return [
    [
        'name' => 'Portfolio',
        'flag' => 'plugins.portfolio',
    ],
    [
        'name' => 'Projects',
        'flag' => 'portfolio.projects.index',
        'parent_id' => 'plugins.portfolio',
    ],
    [
        'name' => 'Create',
        'flag' => 'portfolio.projects.create',
        'parent_flag' => 'portfolio.projects.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'portfolio.projects.edit',
        'parent_flag' => 'portfolio.projects.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'portfolio.projects.destroy',
        'parent_flag' => 'portfolio.projects.index',
    ],

    [
        'name' => 'Service Categories',
        'flag' => 'portfolio.service-categories.index',
        'parent_id' => 'plugins.portfolio',
    ],
    [
        'name' => 'Create',
        'flag' => 'portfolio.service-categories.create',
        'parent_flag' => 'portfolio.service-categories.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'portfolio.service-categories.edit',
        'parent_flag' => 'portfolio.service-categories.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'portfolio.service-categories.destroy',
        'parent_flag' => 'portfolio.service-categories.index',
    ],

    [
        'name' => 'Services',
        'flag' => 'portfolio.services.index',
        'parent_id' => 'plugins.portfolio',
    ],
    [
        'name' => 'Create',
        'flag' => 'portfolio.services.create',
        'parent_flag' => 'portfolio.services.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'portfolio.services.edit',
        'parent_flag' => 'portfolio.services.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'portfolio.services.destroy',
        'parent_flag' => 'portfolio.services.index',
    ],

    [
        'name' => 'Packages',
        'flag' => 'portfolio.packages.index',
        'parent_id' => 'plugins.portfolio',
    ],
    [
        'name' => 'Create',
        'flag' => 'portfolio.packages.create',
        'parent_flag' => 'portfolio.packages.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'portfolio.packages.edit',
        'parent_flag' => 'portfolio.packages.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'portfolio.packages.destroy',
        'parent_flag' => 'portfolio.packages.index',
    ],

    [
        'name' => 'Quotation requests',
        'flag' => 'portfolio.quotation-requests.index',
        'parent_id' => 'plugins.portfolio',
    ],
    [
        'name' => 'Create',
        'flag' => 'portfolio.quotation-requests.create',
        'parent_flag' => 'portfolio.projects.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'portfolio.quotation-requests.edit',
        'parent_flag' => 'portfolio.projects.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'portfolio.quotation-requests.destroy',
        'parent_flag' => 'portfolio.quotation-requests.index',
    ],

    [
        'name' => 'Custom fields',
        'flag' => 'portfolio.custom-fields.index',
        'parent_id' => 'plugins.portfolio',
    ],
    [
        'name' => 'Create',
        'flag' => 'portfolio.custom-fields.create',
        'parent_flag' => 'portfolio.projects.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'portfolio.custom-fields.edit',
        'parent_flag' => 'portfolio.projects.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'portfolio.custom-fields.destroy',
        'parent_flag' => 'portfolio.custom-fields.index',
    ],
];
