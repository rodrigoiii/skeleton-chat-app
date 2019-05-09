<?php

namespace App\Controllers\Auth;

use App\Auth\Traits\Login\Login;
use Core\BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginController extends BaseController
{
    use Login;
}
