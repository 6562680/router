<?php

namespace Gzhegow\Router\Domain\Route;


/**
 * RouteAwareInterface
 */
interface RouteAwareInterface
{
    /**
     * @return Route
     */
    public function getRoute() : Route;
}
