<?php

namespace Core\Middlewares;

use Core\BaseMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class OldInputMiddleware extends BaseMiddleware
{
    /**
     * Make the old_input accessible in twig view.
     *
     * @param  Request $request
     * @param  Response $response
     * @param  callable $next
     * @return callable
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $old_input = null;
        if (isset($_SESSION['old_input']))
        {
            $old_input = $_SESSION['old_input'];
            unset($_SESSION['old_input']);
        }

        if (!is_null($old_input))
        {
            $new_old_input = [];

            foreach ($old_input as $field => $input) {
                if (is_array($input))
                {
                    foreach ($input as $key => $value) {
                        $new_old_input[$field][$key] = $input[$key];
                    }
                }
                else
                {
                    $new_old_input[$field] = $input;
                }
            }

            $this->view->getEnvironment()->addGlobal('old_input', $new_old_input);
        }

        $_SESSION['old_input'] = $request->getParsedBody();

        return $next($request, $response);
    }
}
