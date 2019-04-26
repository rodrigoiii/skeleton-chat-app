<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SkeletonCore\BaseController;

class ExampleController extends BaseController
{
    /**
     * Display home page
     *
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    public function index(Response $response)
    {
        return $this->view->render($response, "home.twig");
    }
}
