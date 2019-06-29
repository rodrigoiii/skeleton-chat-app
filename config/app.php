<?php

return [
    'name' => env("APP_NAME"),
    'env' => env("APP_ENV"),
    'use_dist' => env("USE_DIST"),
    'status_up' => filter_var(env("APP_STATUS_UP"), FILTER_VALIDATE_BOOLEAN),

    'default_timezone' => "Asia/Manila",

    'debug' => is_dev()
];
