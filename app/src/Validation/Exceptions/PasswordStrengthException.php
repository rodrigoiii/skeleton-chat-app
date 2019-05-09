<?php

namespace App\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class PasswordStrengthException extends ValidationException
{
    const MIN_LENGTH = 0;
    const LOWER = 1;
    const UPPER = 2;
    const NUMBER = 3;
    const SPECIAL_CHAR = 4;

    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::MIN_LENGTH => "Password must be at least {{min_length}} character(s).",
            self::LOWER => "Password must have {{lower}} lower case.",
            self::UPPER => "Password must have {{upper}} upper case.",
            self::NUMBER => "Password must have {{number}} number(s).",
            self::SPECIAL_CHAR => "Password must have {{special_char}} special character(s).",
        ]
    ];

    public function chooseTemplate()
    {
        $input = $this->getParam('input');

        if (strlen($input) < $this->getParam('min_length'))
        {
            return self::MIN_LENGTH;
        }
        elseif (preg_match_all("/[a-z]/", $input) < $this->getParam('lower'))
        {
            return self::LOWER;
        }
        elseif (preg_match_all("/[A-Z]/", $input) < $this->getParam('upper'))
        {
            return self::UPPER;
        }
        elseif (preg_match_all("/[0-9]/", $input) < $this->getParam('number'))
        {
            return self::NUMBER;
        }
        elseif (preg_match_all("/[^a-zA-Z0-9\s]/", $input) < $this->getParam('special_char'))
        {
            return self::SPECIAL_CHAR;
        }
    }
}
