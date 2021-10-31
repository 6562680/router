<?php


namespace Gzhegow\Router\Service\RouteCompiler;

use Gzhegow\Router\Domain\Blueprint\Blueprint;
use Gzhegow\Router\Domain\Cors\CorsMiddleware;


/**
 * CorsRouteCompiler
 */
class CorsRouteCompiler implements RouteCompilerInterface
{
    /**
     * @param Blueprint $route
     *
     * @return void
     */
    public function compileRoute($route) : void
    {
        $middlewares = $route->getMiddlewares();

        array_unshift($middlewares, CorsMiddleware::class);

        $route->middlewares(null);
        $route->middlewares($middlewares);
    }


    /**
     * @param Blueprint $route
     *
     * @return bool
     */
    public function supportsRoute($route) : bool
    {
        if (! ( $route instanceof Blueprint )) {
            return false;
        }

        if (! $route->getCors()) {
            return false;
        }

        return true;
    }
}
