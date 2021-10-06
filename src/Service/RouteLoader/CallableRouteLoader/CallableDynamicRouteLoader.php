<?php


namespace Gzhegow\Router\Service\RouteLoader\CallableRouteLoader;

use Gzhegow\Router\Vendor\Helper;
use Gzhegow\Router\Service\RouteLoader\CallableRouteLoader;


/**
 * CallableDynamicRouteLoader
 */
class CallableDynamicRouteLoader extends CallableRouteLoader
{
    /**
     * @param string $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        return ( is_object($source) && is_callable($source) )
            || Helper::filterCallableArrayPublic($source);
    }
}
