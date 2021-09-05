<?php


namespace Gzhegow\Router\Domain\Compiler;

use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Domain\Route\BlueprintRoute;


/**
 * RouteCompilerInterface
 */
interface RouteCompilerInterface
{
    /**
     * @param BlueprintRoute $routeBlueprint
     *
     * @return Route
     */
    public function compile(BlueprintRoute $routeBlueprint) : Route;
}
