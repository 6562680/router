<?php


namespace Gzhegow\Router\Service\RouteLoader;

use Gzhegow\Router\Domain\Route\RouteCollection;


/**
 * CallableRouteLoader
 */
class CallableRouteLoader implements RouteLoaderInterface
{
    /**
     * @param callable    $source
     * @param null|object $newthis
     *
     * @return RouteCollection
     */
    public function loadSource($source, object $newthis = null) : RouteCollection
    {
        $collection = new RouteCollection();

        $source($collection, $newthis ?? $this);

        return $collection;
    }


    /**
     * @param string $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        return is_callable($source);
    }
}
