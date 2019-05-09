<?php

namespace App\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class PasswordVerifyException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => "Password is invalid"
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => "Invert Error Message"
        ]
    ];
}
