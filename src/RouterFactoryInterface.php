<?php

namespace Gzhegow\Router;

use Gzhegow\Router\Domain\Handler\HandlerInterface;
use Gzhegow\Router\Service\RouteLoader\RouteLoaderInterface;
use Gzhegow\Router\Service\RouteCompiler\RouteCompilerInterface;
use Gzhegow\Router\Domain\Handler\Middleware\MiddlewareInterface;
use Gzhegow\Router\Service\ActionProcessor\ActionProcessorInterface;


/**
 * RouterFactoryInterface
 */
interface RouterFactoryInterface
{
    /**
     * @return RouteLoaderInterface
     */
    public function newRouteLoader() : RouteLoaderInterface;

    /**
     * @return RouteCompilerInterface
     */
    public function newRouteCompiler() : RouteCompilerInterface;


    /**
     * @return ActionProcessorInterface
     */
    public function newActionProcessor() : ActionProcessorInterface;


    /**
     * @param mixed $action
     *
     * @return HandlerInterface
     */
    public function newAction($action) : HandlerInterface;

    /**
     * @param mixed                 $middleware
     * @param null|HandlerInterface $next
     *
     * @return MiddlewareInterface
     */
    public function newMiddleware($middleware, HandlerInterface $next = null) : MiddlewareInterface;
}
