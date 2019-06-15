<?php

namespace App\Middlewares;

use Core\BaseMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ExampleMiddleware extends BaseMiddleware
{
    /**
     * [__invoke description]
     *
     * @param  Request $request
     * @param  Response $response
     * @param  callable $next
     * @return callable
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        return $next($request, $response);
    }
}
