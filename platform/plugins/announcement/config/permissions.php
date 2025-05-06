<?php

return [
    [
        'name' => 'Announcements',
        'flag' => 'announcements.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'announcements.create',
        'parent_flag' => 'announcements.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'announcements.edit',
        'parent_flag' => 'announcements.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'announcements.destroy',
        'parent_flag' => 'announcements.index',
    ],
    [
        'name' => 'Announcements',
        'flag' => 'announcements.settings',
        'parent_flag' => 'settings.others',
    ],
];
