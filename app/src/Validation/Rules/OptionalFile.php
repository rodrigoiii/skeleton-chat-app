<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractWrapper;
use Respect\Validation\Validatable;

class OptionalFile extends AbstractWrapper
{
    public function __construct(Validatable $rule)
    {
        $this->validatable = $rule;
    }

    private function isOptional($input)
    {
        if ($input instanceof \SplFileInfo)
        {
            return $input->getSize() <= 0;
        }

        return in_array($input, [null, ''], true);
    }

    public function assert($input)
    {
        if ($this->isOptional($input)) {
            return true;
        }

        return parent::assert($input);
    }

    public function check($input)
    {
        if ($this->isOptional($input)) {
            return true;
        }

        return parent::check($input);
    }

    public function validate($input)
    {
        if ($this->isOptional($input)) {
            return true;
        }

        return parent::validate($input);
    }
}
