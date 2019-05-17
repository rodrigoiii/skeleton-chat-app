<?php

namespace App\Requests;

use Respect\Validation\Validator as v;
use Core\BaseRequest;

class LoginRequest extends BaseRequest
{
    /**
     * Create rules using Respect Validation Library
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => v::notEmpty()->email(),
            'password' => v::notEmpty()
        ];
    }
}
