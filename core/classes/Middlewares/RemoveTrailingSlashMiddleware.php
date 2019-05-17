<?php

namespace Core\Middlewares;

use Core\BaseMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @source  http://www.slimframework.com/docs/v3/cookbook/route-patterns.html
 */
class RemoveTrailingSlashMiddleware extends BaseMiddleware
{
    /**
     * Remove trailing slash in url.
     *
     * @param  Request $request
     * @param  Response $response
     * @param  callable $next
     * @return callable
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        if ($path != '/' && substr($path, -1) == '/') {
            // permanently redirect paths with a trailing slash
            // to their non-trailing counterpart
            $uri = $uri->withPath(substr($path, 0, -1));

            if ($request->getMethod() == 'GET') {
                return $response->withRedirect($uri->getPath());
            }

            return $next($request->withUri($uri), $response);
        }

        return $next($request, $response);
    }
}
