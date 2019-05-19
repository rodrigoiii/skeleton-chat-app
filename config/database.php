<?php

return [
    'host'     => env("DB_HOSTNAME"),
    'port'     => env("DB_PORT"),
    'username' => env("DB_USERNAME"),
    'password' => env("DB_PASSWORD"),
    'database' => env("DB_NAME"),

    'driver'    => "mysql",
    'charset'   => "utf8",
    'collation' => "utf8_unicode_ci",
    'prefix'    => "",

    'strict' => true,
    'engine' => null
];
