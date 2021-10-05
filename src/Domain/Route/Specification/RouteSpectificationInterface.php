<?php

namespace Gzhegow\Router\Domain\Route\Specification;

use Gzhegow\Router\Domain\Route\Route;


/**
 * RouteSpectificationInterface
 */
interface RouteSpectificationInterface
{
    /**
     * @param Route $route
     *
     * @return bool
     */
    public function isMatch(Route $route) : bool;
}
