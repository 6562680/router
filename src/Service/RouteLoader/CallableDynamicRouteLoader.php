<?php


namespace Gzhegow\Router\Service\RouteLoader;

use Gzhegow\Router\Vendor\Helper;
use Gzhegow\Router\Domain\Route\RouteCollection;


/**
 * CallableDynamicRouteLoader
 */
class CallableDynamicRouteLoader implements RouteLoaderInterface
{
    /**
     * @param mixed                $source
     * @param null|RouteCollection $collection
     *
     * @return RouteCollection
     */
    public function loadSource($source, RouteCollection $collection = null) : RouteCollection
    {
        $collection = $collection ?? new RouteCollection();

        call_user_func($source, $collection);

        return $collection;
    }


    /**
     * @param string $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        return $source instanceof \Closure
            || Helper::filterCallableArrayPublic($source);
    }
}
