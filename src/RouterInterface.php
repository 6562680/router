<?php

namespace Gzhegow\Router;

use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Domain\Route\RouteCollection;
use Gzhegow\Router\Domain\Configuration\PatternCollection;
use Gzhegow\Router\Domain\Configuration\MiddlewareCollection;
use Gzhegow\Router\Domain\Route\Specification\RouteSpectificationInterface;


/**
 * RouterInterface
 */
interface RouterInterface
{
    /**
     * @return RouterContainerInterface
     */
    public function getRouterContainer() : RouterContainerInterface;


    /**
     * @return RouteCollection
     */
    public function getRouteCollection() : RouteCollection;


    /**
     * @return null|Route
     */
    public function getRouteCurrent() : ?Route;


    /**
     * @return MiddlewareCollection
     */
    public function getMiddlewareCollection() : MiddlewareCollection;

    /**
     * @return PatternCollection
     */
    public function getPatternCollection() : PatternCollection;


    /**
     * @return RouterCacheInterface
     */
    public function getRouterCache() : RouterCacheInterface;


    /**
     * @param mixed $source
     *
     * @return static
     */
    public function load($source);


    /**
     * @param Route      $route
     * @param null|mixed $payload
     * @param mixed      ...$arguments
     *
     * @return null|int|mixed
     */
    public function handle(Route $route, $payload = null, ...$arguments);


    /**
     * @param RouteSpectificationInterface $routeSpecification
     *
     * @return Route[]
     */
    public function matchAll(RouteSpectificationInterface $routeSpecification) : array;

    /**
     * @param RouteSpectificationInterface $routeSpecification
     *
     * @return null|Route
     */
    public function match(RouteSpectificationInterface $routeSpecification) : ?Route;
}
