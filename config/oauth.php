<?php
return [
    'secrets'=>[
        'client_id' => env('CLIENT_ID'),
        'client_secret' => env('CLIENT_SECRET'),
    ],
    'roles' => [
        'customer' => 'customer',
        'banker' => 'banker',
        'manager' => 'manager',
    ]
];