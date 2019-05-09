<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class Test extends AbstractRule
{
    public function validate($input)
    {
        return $input === "test";
    }
}
