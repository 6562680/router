<?php


namespace Gzhegow\Router\Domain\Loader;

use Gzhegow\Router\Domain\Route\BlueprintRouteGroup;


/**
 * CallableRouteLoader
 */
class CallableRouteLoader implements RouteLoaderInterface
{
    /**
     * @param string $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        return is_callable($source);
    }


    /**
     * @param BlueprintRouteGroup $group
     * @param string              $source
     *
     * @return void
     */
    public function loadSource(BlueprintRouteGroup $group, $source) : void
    {
        call_user_func($source, $group);
    }
}
