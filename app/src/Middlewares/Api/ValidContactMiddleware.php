<?php

namespace App\Middlewares\Api;

use App\Models\ContactRequest;
use App\Models\User;
use Core\BaseMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;

class ValidContactMiddleware extends BaseMiddleware
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
        $URIs = explode("/", trim($request->getUri()->getPath(), "/"));
        if ($URIs[0] === "api") // shift the api value if it is exist
        {
            array_shift($URIs);
        }

        $to_id = $URIs[1];

        $user2 = User::find($to_id);

        if (!is_null($user2))
        {
            $login_token = $request->getParam("login_token");
            $authUser = User::findByLoginToken($login_token);

            if (ContactRequest::areFriends($authUser, $user2))
            {
                return $next($request, $response);
            }
        }

        throw new NotFoundException($request, $response);
    }
}
