<?php


namespace Gzhegow\Router\Domain\Processor\Middleware;

use Gzhegow\Router\Domain\Handler\HandlerInterface;


/**
 * CallableMiddlewareProcessor
 */
class CallableMiddlewareProcessor implements MiddlewareProcessorInterface
{
    /**
     * @param mixed            $middleware
     * @param HandlerInterface $next
     * @param mixed            $payload
     * @param mixed            ...$arguments
     *
     * @return null|int|mixed
     */
    public function processMiddleware($middleware, HandlerInterface $next, $payload, ...$arguments)
    {
        $result = null;

        // $middlewareClass = $middleware;
        //
        // $route = $this->getRouteCurrent();
        //
        // $middlewareObject = $this->newAutowired($middlewareClass, $route->getBindings());
        //
        // $result = $this->callAutowired(null, [ $middlewareObject, 'handle' ], $route->getBindings());

        return $result;
    }


    /**
     * @param mixed $middleware
     *
     * @return bool
     */
    public function supportsMiddleware($middleware) : bool
    {
        return is_callable($middleware);
    }
}
