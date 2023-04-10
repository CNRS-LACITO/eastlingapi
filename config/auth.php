<?php
////php artisan jwt:secret => g4GMDAZK5KQwqpx2nvTM9dRur2oFbOLCyU4T2XUjgWTduaq1MIsVZEPIw3zKT9Nv
return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],

    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \App\Models\User::class
        ]
    ]
];