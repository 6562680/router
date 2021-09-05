<?php


namespace Gzhegow\Router\Domain\Processor\Middleware;

use Gzhegow\Router\Domain\Handler\HandlerInterface;
use Gzhegow\Router\Domain\Configuration\MiddlewareGroupCollection;


/**
 * NamedGroupMiddlewareProcessor
 */
class NamedGroupMiddlewareProcessor implements MiddlewareProcessorInterface
{
    /**
     * @var MiddlewareGroupCollection
     */
    protected $middlewareGroupCollection;


    /**
     * Constructor
     *
     * @param MiddlewareGroupCollection $middlewareGroupCollection
     */
    public function __construct(MiddlewareGroupCollection $middlewareGroupCollection)
    {
        $this->middlewareGroupCollection = $middlewareGroupCollection;
    }


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

        // $route = $this->getRouteCurrent();
        //
        // $middlewareClass = $this->configuration->getMiddleware($middleware);
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
        return $this->middlewareGroupCollection->hasMiddlewareGroup($middleware);
    }
}
