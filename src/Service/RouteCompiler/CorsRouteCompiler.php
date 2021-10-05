<?php


namespace Gzhegow\Router\Service\RouteCompiler;

use Gzhegow\Router\Domain\Blueprint\Blueprint;


/**
 * CorsRouteCompiler
 */
class CorsRouteCompiler implements RouteCompilerInterface
{
    /**
     * @var mixed
     */
    protected $corsMiddleware;


    /**
     * Constructor
     *
     * @param mixed $corsMiddleware
     */
    public function __construct($corsMiddleware)
    {
        $this->corsMiddleware = $corsMiddleware;
    }


    /**
     * @param Blueprint $route
     *
     * @return void
     */
    public function compileRoute($route) : void
    {
        $middlewares = $route->getMiddlewares();
        array_unshift($middlewares, $this->corsMiddleware);

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
