<?php


namespace Gzhegow\Router\Domain\Loader;

use Gzhegow\Router\Domain\Route\BlueprintRouteGroup;


/**
 * RouteLoaderInterface
 */
interface RouteLoaderInterface
{
    /**
     * @param mixed $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool;


    /**
     * @param BlueprintRouteGroup $group
     * @param mixed               $source
     *
     * @return void
     */
    public function loadSource(BlueprintRouteGroup $group, $source) : void;
}
