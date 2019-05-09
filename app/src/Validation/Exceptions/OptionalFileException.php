<?php

namespace App\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class OptionalFileException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'The value must be optional'
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'The value must not be optional'
        ],
    ];
}
