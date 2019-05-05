<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class PasswordMatch extends AbstractRule
{
    public $password;

    public function __construct($password)
    {
        $this->password = $password;
    }

    /**
     * Validate the input provided.
     *
     * @param  mixed $confirm_password
     * @return boolean
     */
    public function validate($confirm_password)
    {
        return $this->password === $confirm_password;
    }
}
