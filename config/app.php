<?php

return [
    'name' => env("APP_NAME"),
    'env' => env("APP_ENV"),
    'use_dist' => env("USE_DIST"),
    'status_up' => env("APP_STATUS_UP"),

    'debug' => is_dev()
];
