<?php
//$url = parse_url(getenv("DATABASE_URL"));
//
//$host = $url["host"];
//$username = $url["user"];
//$password = $url["pass"];
//$database = substr($url["path"], 1);
    return [
        /*
        |--------------------------------------------------------------------------
        | Default Database Connection Name
        |--------------------------------------------------------------------------
        |
        | Here you may specify which of the database connections below you wish
        | to use as your default connection for all database work. Of course
        | you may use many connections at once using the Database library.
        |
        */
        'default' => env('DB_CONNECTION', 'mysql'),

        'migrations' => 'migrations',

        'connections' => [
            'mysql' => [
                'driver' => 'mysql',
                'host' => env('DB_HOST', 'sql2.freemysqlhosting.net'),
                'port' => env('DB_PORT', '3306'),
                'database' => env('DB_DATABASE', 'sql2225571'),
                'username' => env('DB_USERNAME', 'sql2225571'),
                'password' => env('DB_PASSWORD', 'pD9*mD1!'),
                'unix_socket' => env('DB_SOCKET', ''),
//                'charset' => 'utf8mb4',
//                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ],

//            'pgsql' => array(
//                'driver'   => 'pgsql',
//                'host'     => $host,
//                'database' => $database,
//                'username' => $username,
//                'password' => $password,
//                'charset'  => 'utf8',
//                'prefix'   => '',
//                'schema'   => 'public',
//            ),
        ]
];