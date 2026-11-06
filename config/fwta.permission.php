<?php

return [
    'core' => [
        'basic' => [
            'Dashboard' => [
                'dashboard.index' => 'View',
            ],
            'domain' => [
                'domain.index' => 'View',
                'domain.create' => 'Create',
                'domain.edit' => 'Edit',
                'domain.store' => 'Save',
            ],
            'user' => [
                'user.index' => 'View',
                'user.create' => 'Create',
                'user.edit' => 'Edit',
                'user.store' => 'Save',
                'user.destroy' => 'Delete',
                'user.forceDestroy' => "Force Delete"
            ],
            'role' => [
                'role.index' => 'View',
                'role.create' => 'Create',
                'role.edit' => 'Edit',
                'role.store' => 'Save',
                'role.destroy' => 'Delete',
            ]
        ]
    ]
];
