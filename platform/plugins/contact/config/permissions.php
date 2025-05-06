<?php

return [
    [
        'name' => 'Contact',
        'flag' => 'contacts.index',
        'parent_flag' => 'core.cms',
    ],
    [
        'name' => 'Edit',
        'flag' => 'contacts.edit',
        'parent_flag' => 'contacts.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'contacts.destroy',
        'parent_flag' => 'contacts.index',
    ],
    [
        'name' => 'Custom Fields',
        'flag' => 'contact.custom-fields',
        'parent_flag' => 'contacts.index',
    ],
    [
        'name' => 'Contact',
        'flag' => 'contact.settings',
        'parent_flag' => 'settings.others',
    ],
];
