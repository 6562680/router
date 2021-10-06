<?php


namespace Gzhegow\Router\Service\RouteLoader;

use Gzhegow\Router\Vendor\Helper;
use Gzhegow\Router\Domain\Route\RouteCollection;


/**
 * FilePhpRouteLoader
 */
class FilePhpRouteLoader implements RouteLoaderInterface
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

        require $realpath = Helper::theRealpath($source);

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
