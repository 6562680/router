<?php

namespace Gzhegow\Router\Domain\Specification;

use Gzhegow\Router\Domain\Route\Route;


/**
 * SpecificationInterface
 */
interface SpecificationInterface
{
    /**
     * @param Route $route
     *
     * @return bool
     */
    public function isMatch(Route $route) : bool;
}
