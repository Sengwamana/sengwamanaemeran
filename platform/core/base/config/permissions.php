<?php

return [
    [
        'name' => 'System',
        'flag' => 'core.system',
    ],
    [
        'name' => 'CMS',
        'flag' => 'core.cms',
    ],
    [
        'name' => 'Manage license',
        'flag' => 'core.manage.license',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'Cronjob',
        'flag' => 'systems.cronjob',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'Tools',
        'flag' => 'core.tools',
    ],
    [
        'name' => 'Import/Export Data',
        'flag' => 'tools.data-synchronize',
        'parent_flag' => 'core.tools',
    ],
];
