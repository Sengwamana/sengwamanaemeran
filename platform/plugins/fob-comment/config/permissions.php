<?php

return [
    [
        'name' => 'FOB Comments',
        'flag' => 'fob-comment.index',
    ],
    [
        'name' => 'List',
        'flag' => 'fob-comment.comments.index',
        'parent_flag' => 'fob-comment.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'fob-comment.comments.edit',
        'parent_flag' => 'fob-comment.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'fob-comment.comments.destroy',
        'parent_flag' => 'fob-comment.index',
    ],
    [
        'name' => 'Reply',
        'flag' => 'fob-comment.comments.reply',
        'parent_flag' => 'fob-comment.index',
    ],
    [
        'name' => 'Settings',
        'flag' => 'fob-comment.settings',
        'parent_flag' => 'fob-comment.index',
    ],
];
