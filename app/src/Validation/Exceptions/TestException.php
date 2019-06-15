<?php

namespace App\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class TestException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => "Testing my friend"
        ]
    ];
}
