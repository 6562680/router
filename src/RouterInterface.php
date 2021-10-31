<?php

namespace Gzhegow\Router;

use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Domain\Route\Specification\RouteSpecificationInterface;


/**
 * RouterInterface
 */
interface RouterInterface
{
    /**
     * @param Route $route
     * @param mixed ...$arguments
     *
     * @return null|int|mixed
     */
    public function handle(Route $route, ...$arguments);


    /**
     * @param null|RouteSpecificationInterface $routeSpecification
     * @param null|int                         $limit
     * @param null|int                         $offset
     *
     * @return Route[]
     */
    public function matchAll(?RouteSpecificationInterface $routeSpecification, int $limit = null, int $offset = null) : array;

    /**
     * @param null|RouteSpecificationInterface $routeSpecification
     * @param null|int                         $offset
     *
     * @return null|Route
     */
    public function match(?RouteSpecificationInterface $routeSpecification, int $offset = null) : ?Route;
}
