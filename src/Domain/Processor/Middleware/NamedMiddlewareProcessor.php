<?php


namespace Gzhegow\Router\Domain\Processor\Middleware;

use Gzhegow\Router\Domain\Handler\HandlerInterface;
use Gzhegow\Router\Domain\Configuration\MiddlewareCollection;


/**
 * NamedMiddlewareProcessor
 */
class NamedMiddlewareProcessor implements MiddlewareProcessorInterface
{
    /**
     * @var MiddlewareCollection
     */
    protected $middlewareCollection;


    /**
     * Constructor
     *
     * @param MiddlewareCollection $middlewareCollection
     */
    public function __construct(MiddlewareCollection $middlewareCollection)
    {
        $this->middlewareCollection = $middlewareCollection;
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
        return $this->middlewareCollection->hasMiddleware($middleware);
    }
}
