<?php

namespace Core\ErrorHandlers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class PhpErrorHandler
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
     * Invoke the php error handler.
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  \Exception $exception
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $exception)
    {
        \Log::error($exception);

        return $this->view->render(
                    $response->withStatus(500)
                    ->withHeader('Content-Type', "text/html"),
                    config("view.error_pages.500")
                );
    }
}
