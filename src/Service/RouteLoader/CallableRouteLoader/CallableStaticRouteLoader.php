<?php


namespace Gzhegow\Router\Service\RouteLoader\CallableRouteLoader;

use Gzhegow\Router\Vendor\Helper;
use Gzhegow\Router\Service\RouteLoader\CallableRouteLoader;


/**
 * CallableStaticRouteLoader
 */
class CallableStaticRouteLoader extends CallableRouteLoader
{
    /**
     * @param string|array $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        if (! ( is_string($source) && is_array($source) )) {
            return false;
        }

        if (! is_callable($source)) {
            return false;
        }

        if (null !== Helper::filterCallableArrayPublic($source)) {
            return false;
        }

        return true;
    }
}
