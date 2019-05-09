<?php

namespace App\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class PasswordMatchException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => "Password and confirm password do not match"
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => "Invert Error Message"
        ]
    ];
}
