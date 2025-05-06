<?php

return [
    [
        'name' => 'Newsletters',
        'flag' => 'newsletter.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'newsletter.destroy',
        'parent_flag' => 'newsletter.index',
    ],
    [
        'name' => 'Newsletters',
        'flag' => 'newsletter.settings',
        'parent_flag' => 'settings.others',
    ],
];
