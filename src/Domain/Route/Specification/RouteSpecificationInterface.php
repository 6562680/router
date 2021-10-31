<?php

namespace Gzhegow\Router\Domain\Route\Specification;

use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Domain\Route\RouteCollection;


/**
 * RouteSpecificationInterface
 */
interface RouteSpecificationInterface
{
    /**
     * @param RouteCollection $routeCollection
     *
     * @return Route[]
     */
    public function matches(RouteCollection $routeCollection) : array;

    /**
     * @param Route $route
     *
     * @return null|Route
     */
    public function match(Route $route) : ?Route;
}
