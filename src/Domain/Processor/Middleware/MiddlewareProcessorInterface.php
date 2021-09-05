<?php


namespace Gzhegow\Router\Domain\Processor\Middleware;

use Gzhegow\Router\Domain\Handler\HandlerInterface;


/**
 * MiddlewareProcessorInterface
 */
interface MiddlewareProcessorInterface
{
    /**
     * @param mixed $middleware
     *
     * @return bool
     */
    public function supportsMiddleware($middleware) : bool;


    /**
     * @param mixed            $middleware
     * @param HandlerInterface $next
     * @param mixed            $payload
     * @param mixed            ...$arguments
     *
     * @return null|int|mixed
     */
    public function processMiddleware($middleware, HandlerInterface $next, $payload, ...$arguments);
}
