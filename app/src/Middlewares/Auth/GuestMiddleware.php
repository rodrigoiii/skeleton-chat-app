<?php

namespace App\Middlewares\Auth;

use App\Auth\Traits\Middlewares\Guest;
use Core\BaseMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GuestMiddleware extends BaseMiddleware
{
    use Guest;
}
