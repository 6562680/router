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
     * @return RouterCacheInterface
     */
    public function getRouterCache() : RouterCacheInterface;


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
     * @param mixed $source
     *
     * @return static
     */
    public function load($source);


    /**
     * @param Route $route
     * @param mixed ...$arguments
     *
     * @return null|int|mixed
     */
    public function handle(Route $route, ...$arguments);


    /**
     * @param null|RouteSpectificationInterface $routeSpecification
     * @param null|int                          $limit
     * @param null|int                          $offset
     *
     * @return Route[]
     */
    public function matchAll(RouteSpectificationInterface $routeSpecification = null, int $limit = null, int $offset = null) : array;

    /**
     * @param null|RouteSpectificationInterface $routeSpecification
     * @param null|int                          $offset
     *
     * @return null|Route
     */
    public function match(RouteSpectificationInterface $routeSpecification = null, int $offset = null) : ?Route;
}
