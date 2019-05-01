<?php

namespace Core\ErrorHandlers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class NotFoundHandler
{
    /**
     * @var Twig
     */
    protected $view;

    /**
     * Initialize the twig view.
     *
     * @param Twig $view
     */
    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    /**
     * Invoke the not found handler.
     *
     * @param  Request $request
     * @param  Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        return $this->view->render(
                    $response->withStatus(404)
                    ->withHeader('Content-Type', "text/html"),
                    config("view.error_pages.404")
                );
    }
}
