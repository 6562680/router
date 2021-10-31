<?php


namespace Gzhegow\Router\Service\RouteLoader;

use Gzhegow\Router\Vendor\Helper;
use Gzhegow\Router\Domain\Route\RouteCollection;


/**
 * FileRequireRouteLoader
 */
class FileRequireRouteLoader implements RouteLoaderInterface
{
    /**
     * @param mixed       $source
     * @param null|object $newthis
     *
     * @return RouteCollection
     */
    public function loadSource($source, object $newthis = null) : RouteCollection
    {
        $collection = ( function () use ($source) {
            $collection = new RouteCollection();

            require Helper::theRealpath($source);

            return $collection;
        } )->call($newthis ?? $this);

        return $collection;
    }


    /**
     * @param string $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        return (bool) Helper::filterFilePhp($source);
    }
}
