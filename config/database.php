<?php

return [
    'host'     => env('DB_HOSTNAME', "localhost"),
    'port'     => env('DB_PORT', "3306"),
    'username' => env('DB_USERNAME', "root"),
    'password' => env('DB_PASSWORD'),
    'database' => env('DB_NAME'),

    'driver'    => "mysql",
    'charset'   => "utf8",
    'collation' => "utf8_unicode_ci",
    'prefix'    => "",

    'strict' => true,
    'engine' => null
];
