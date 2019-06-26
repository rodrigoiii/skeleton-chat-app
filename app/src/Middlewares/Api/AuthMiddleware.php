<?php

namespace App\Middlewares\Api;

use App\Models\User;
use Core\BaseMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthMiddleware extends BaseMiddleware
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

        if (!is_null($authUser))
        {
            return $next($request, $response);
        }

        return $response->withJson([
            'success' => false,
            'message' => "Unauthorized to access this endpoint"
        ]);
    }
}
