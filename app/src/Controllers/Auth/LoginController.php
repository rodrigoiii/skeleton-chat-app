<?php

namespace App\Controllers\Auth;

use App\Auth\Auth;
use App\Requests\LoginRequest;
use Core\BaseController;
use Psr\Http\Message\ResponseInterface as Response;

class LoginController extends BaseController
{
    /**
     * Display login page
     *
     * @param  Response $response
     * @return Response
     */
    public function getLogin(Response $response)
    {
        return $this->view->render($response, "auth/login.twig");
    }

    /**
     * Post user credential
     * @param  LoginRequest $_request
     * @param  Response     $response
     * @return Response
     */
    public function postLogin(LoginRequest $_request, Response $response)
    {
        $inputs = $_request->getParams();

        if ($user = Auth::validateCredential($inputs['email'], $inputs['password']))
        {
            // login the user
            Auth::logInByUserId($user->getId());

            $this->flash->addMessage('success', "Successfully login!");
            return $response->withRedirect($this->router->pathFor('auth.home'));
        }

        $this->flash->addMessage('danger', "Invalid email or password!");
        return $response->withRedirect($this->router->pathFor('auth.login'));
    }

    /**
     * Logout user
     *
     * @param  Response $response
     * @return Response
     */
    public function logout(Response $response)
    {
        Auth::logOut();
        return $response->withRedirect($this->router->pathFor('auth.login'));
    }
}
