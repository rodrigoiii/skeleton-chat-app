<?php

namespace Core\ErrorHandlers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class NotAllowedHandler
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
     * Invoke the not allowed handler.
     *
     * @param  Request $request
     * @param  Response $response
     * @param  array $methods
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $methods)
    {
        return $this->view->render(
                    $response->withStatus(405)
                    ->withHeader('Allow', implode(', ', $methods))
                    ->withHeader('Content-Type', "text/html"),
                    config("view.error_pages.405")
                );
    }
}
