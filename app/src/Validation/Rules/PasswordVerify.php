<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class PasswordVerify extends AbstractRule
{
    private $hash_password;

    public function __construct($hash_password)
    {
        $this->hash_password = $hash_password;
    }

    /**
     * Validate the input provided.
     *
     * @param  mixed $input
     * @return boolean
     */
    public function validate($input)
    {
        return password_verify($input, $this->hash_password);
    }
}
