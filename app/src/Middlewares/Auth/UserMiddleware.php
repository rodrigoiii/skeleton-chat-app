<?php

namespace App\Middlewares\Auth;

use App\Auth\Traits\Middlewares\User;
use Core\BaseMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserMiddleware extends BaseMiddleware
{
    use User;
}
