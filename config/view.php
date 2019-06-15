<?php

return [
    # error pages path, relative in resources/views path
    'error_pages' => [
        '500' => "errors/page-500.twig",
        '405' => "errors/page-405.twig",
        '404' => "errors/page-404.twig",
        '403' => "errors/page-403.twig",
        'under_construction' => "errors/under-construction.twig"
    ],

    'functions' => ["str_title"]
];
