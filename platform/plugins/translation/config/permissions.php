<?php

return [
    [
        'name' => 'Localization',
        'flag' => 'plugins.translation',
        'parent_flag' => 'settings.index',
    ],
    [
        'name' => 'Locales',
        'flag' => 'translations.locales',
        'parent_flag' => 'plugins.translation',
    ],
    [
        'name' => 'Theme translations',
        'flag' => 'translations.theme-translations',
        'parent_flag' => 'plugins.translation',
    ],
    [
        'name' => 'Other translations',
        'flag' => 'translations.index',
        'parent_flag' => 'plugins.translation',
    ],
    [
        'name' => 'Export Theme translations',
        'flag' => 'theme-translations.export',
        'parent_flag' => 'tools.data-synchronize',
    ],
    [
        'name' => 'Export Other Translations',
        'flag' => 'other-translations.export',
        'parent_flag' => 'tools.data-synchronize',
    ],
    [
        'name' => 'Import Theme Translations',
        'flag' => 'theme-translations.import',
        'parent_flag' => 'tools.data-synchronize',
    ],
    [
        'name' => 'Import Other Translations',
        'flag' => 'other-translations.import',
        'parent_flag' => 'tools.data-synchronize',
    ],
];
