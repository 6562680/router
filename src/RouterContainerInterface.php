<?php

namespace Gzhegow\Router;

use Psr\Container\ContainerInterface;
use Gzhegow\Router\Domain\Route\RouteCollection;
use Gzhegow\Router\Domain\Configuration\PatternCollection;
use Gzhegow\Router\Service\RouteLoader\RouteLoaderInterface;
use Gzhegow\Router\Domain\Configuration\MiddlewareCollection;
use Gzhegow\Router\Service\RouteCompiler\RouteCompilerInterface;
use Gzhegow\Router\Service\ActionProcessor\ActionProcessorInterface;


/**
 * RouterContainerInterface
 */
interface RouterContainerInterface extends ContainerInterface
{
    /**
     * @return RouterFactoryInterface
     */
    public function getRouterFactory() : RouterFactoryInterface;


    /**
     * @return RouteLoaderInterface
     */
    public function getRouteLoader() : RouteLoaderInterface;

    /**
     * @return RouteCompilerInterface
     */
    public function getRouteCompiler() : RouteCompilerInterface;


    /**
     * @return ActionProcessorInterface
     */
    public function getActionProcessor() : ActionProcessorInterface;


    /**
     * @return mixed
     */
    public function getCorsMiddleware();


    /**
     * @return RouteCollection
     */
    public function getRouteCollection() : RouteCollection;


    /**
     * @return MiddlewareCollection
     */
    public function getMiddlewareCollection() : MiddlewareCollection;

    /**
     * @return PatternCollection
     */
    public function getPatternCollection() : PatternCollection;


    /**
     * @param string $id
     *
     * @return mixed
     */
    public function get(string $id);

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has(string $id) : bool;

    /**
     * @param string $id
     * @param mixed  $value
     *
     * @return static
     */
    public function set(string $id, $value);


    /**
     * @param string|object $objectOrClass
     * @param null|array    $bindings
     *
     * @return object
     */
    public function new($objectOrClass, array $bindings = null) : object;

    /**
     * @param null|object            $newthis
     * @param string|object|callable $callable
     * @param null|array             $parameters
     *
     * @return mixed
     */
    public function call(?object $newthis, $callable, array $parameters = null);
}
