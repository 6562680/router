<?php

namespace Gzhegow\Router\Domain\Factory;

use Gzhegow\Router\Domain\Handler\HandlerInterface;
use Gzhegow\Router\Domain\Handler\Middleware\MiddlewareInterface;


/**
 * RouterFactoryInterface
 */
interface RouterFactoryInterface
{
    /**
     * @param string|callable|HandlerInterface|mixed $action
     *
     * @return HandlerInterface
     */
    public function newAction($action) : HandlerInterface;

    /**
     * @param string|object|callable|MiddlewareInterface|mixed $middleware
     * @param HandlerInterface                                 $next
     *
     * @return MiddlewareInterface
     */
    public function newMiddleware($middleware, HandlerInterface $next) : MiddlewareInterface;

    /**
     * @param string|object $objectOrClass
     * @param null|array    $parameters
     *
     * @return object
     */
    public function newObject($objectOrClass, array $parameters = null) : object;
}
