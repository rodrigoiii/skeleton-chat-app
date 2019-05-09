<?php

namespace App\Auth\Traits\Middlewares;

use App\Auth\Auth;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait User
{
    /**
     * Block the request of non logged in user
     *
     * @param  Request $request
     * @param  Response $response
     * @param  callable $next
     * @return callable
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (Auth::check())
        {
            $this->view->getEnvironment()->addGlobal('auth_user', Auth::user());
            return $next($request, $response);
        }

        return $this->redirectHandler($response);
    }

    public function redirectHandler(Response $response)
    {
        $this->flash->addMessage('error', "Unauthorized to access the page!");
        return $response->withRedirect($this->router->pathFor('auth.login'));
    }
}
