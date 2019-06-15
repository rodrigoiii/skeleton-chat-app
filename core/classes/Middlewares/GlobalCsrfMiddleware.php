<?php

namespace Core\Middlewares;

use Core\BaseMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GlobalCsrfMiddleware extends BaseMiddleware
{
    /**
     * Make accessible the csrf in twig view.
     *
     * @param  Request $request
     * @param  Response $response
     * @param  callable $next
     * @return callable
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $name_key  = $this->csrf->getTokenNameKey();
        $value_key = $this->csrf->getTokenValueKey();
        $name  = $request->getAttribute($name_key);
        $value = $request->getAttribute($value_key);

        $json_token = json_encode([$name_key => $name, $value_key => $value]);

        $this->view->getEnvironment()->addGlobal('csrf', [
            'field' => '
                <input type="hidden" name="'.$name_key.'" value="'.$name.'">
                <input type="hidden" name="'.$value_key.'" value="'.$value.'">
            ',
            'json' => $json_token
        ]);

        $response = $response->withAddedHeader('X-CSRF-TOKEN', $json_token);

        return $next($request, $response);
    }
}
