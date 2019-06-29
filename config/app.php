<?php

return [
    'name' => env("APP_NAME"),
    'env' => env("APP_ENV"),
    'status_up' => filter_var(env("APP_STATUS_UP"), FILTER_VALIDATE_BOOLEAN),

    'debug' => is_dev()
];
