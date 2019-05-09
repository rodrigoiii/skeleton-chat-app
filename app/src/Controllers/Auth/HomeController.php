<?php

namespace App\Controllers\Auth;

use Core\BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController extends BaseController
{
    /**
     * Display home page
     *
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    public function index(Response $response)
    {
        return $this->view->render($response, "auth/home.twig");
    }
}
