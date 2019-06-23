<?php

namespace App\Middlewares;

use Core\BaseMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SendMessageMiddleware extends BaseMiddleware
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
        $login_token = $request->getParam("login_token");
        $authUser = User::findByLoginToken($login_token);

        \Core\Log::debug(print_r($request->getParams(), true));

        return $next($request, $response);
    }
}
