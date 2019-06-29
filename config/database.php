<?php

return [
    'host'     => env("DB_HOSTNAME"),
    'port'     => env("DB_PORT"),
    'username' => env("DB_USERNAME"),
    'password' => env("DB_PASSWORD"),
    'database' => env("DB_NAME"),

    'driver'    => "mysql",
    'charset'   => "utf8mb4",
    'collation' => "utf8mb4_general_ci",
    'prefix'    => "",

    'strict' => true,
    'engine' => null
];
