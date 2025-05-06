<?php

return [
    [
        'name' => 'Settings',
        'flag' => 'settings.index',
    ],
    [
        'name' => 'Common',
        'flag' => 'settings.common',
        'parent_flag' => 'settings.index',
    ],
    [
        'name' => 'General',
        'flag' => 'settings.options',
        'parent_flag' => 'settings.common',
    ],
    [
        'name' => 'Email',
        'flag' => 'settings.email',
        'parent_flag' => 'settings.common',
    ],
    [
        'name' => 'Media',
        'flag' => 'settings.media',
        'parent_flag' => 'settings.common',
    ],
    [
        'name' => 'Admin Appearance',
        'flag' => 'settings.admin-appearance',
        'parent_flag' => 'settings.common',
    ],
    [
        'name' => 'Cache',
        'flag' => 'settings.cache',
        'parent_flag' => 'settings.common',
    ],
    [
        'name' => 'Datatables',
        'flag' => 'settings.datatables',
        'parent_flag' => 'settings.common',
    ],
    [
        'name' => 'Email Rules',
        'flag' => 'settings.email.rules',
        'parent_flag' => 'settings.common',
    ],
    [
        'name' => 'Others',
        'flag' => 'settings.others',
        'parent_flag' => 'settings.index',
    ],
];
