<?php


namespace Gzhegow\Router\Service\RouteLoader;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Gzhegow\Router\Exceptions\RuntimeException;
use Gzhegow\Router\Domain\Route\RouteCollection;


/**
 * SimpleCacheRouteLoader
 */
class SimpleCacheRouteLoader implements RouteLoaderInterface
{
    /**
     * @param CacheInterface       $source
     * @param null|RouteCollection $collection
     *
     * @return RouteCollection
     */
    public function loadSource($source, RouteCollection $collection = null) : RouteCollection
    {
        $collection = $collection ?? new RouteCollection();

        try {
            if ($source->has(SimpleCacheRouteLoader::class)) {
                $routes = $source->get(SimpleCacheRouteLoader::class);

                $collection->addRoutes($routes);
            }
        }
        catch ( InvalidArgumentException $e ) {
            throw new RuntimeException($e->getMessage(), null, $e);
        }

        return $collection;
    }


    /**
     * @param string $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        return $source instanceof CacheInterface;
    }
}
