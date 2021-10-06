<?php


namespace Gzhegow\Router;

use Psr\SimpleCache\CacheInterface;
use Gzhegow\Router\Exceptions\RuntimeException;
use Gzhegow\Router\Domain\Route\RouteCollection;


/**
 * RouterCache
 */
class RouterCache implements RouterCacheInterface
{
    /**
     * @var CacheInterface
     */
    protected $cache;


    /**
     * Constructor
     *
     * @param null|CacheInterface $cache
     */
    public function __construct(?CacheInterface $cache)
    {
        $this->cache = $cache;
    }


    /**
     * @return static
     */
    public function clear()
    {
        if ($this->cache) {
            $this->cache->clear();
        }

        return $this;
    }


    /**
     * @param RouteCollection $routeCollection
     *
     * @return RouteCollection
     */
    protected function assertRouteCollection($routeCollection) : RouteCollection
    {
        return $routeCollection;
    }


    /**
     * @param \Closure $func
     * @param null|int $ttl
     * @param null|int $key
     *
     * @return RouteCollection
     */
    public function remember(\Closure $func, int $ttl = null, int $key = null) : RouteCollection
    {
        $key = $key ?? __METHOD__;

        try {
            $isExpired = ! ( $this->cache && $this->cache->has($key) );

            $routeCollection = $isExpired ? $func() : null;

            $routeCollection = $this->assertRouteCollection($routeCollection);

            if ($this->cache) {
                $this->cache->set($key, $routeCollection, $ttl);
            }
        }
        catch ( \Psr\SimpleCache\InvalidArgumentException $e ) {
            throw new RuntimeException($e->getMessage(), null, $e);
        }

        return $routeCollection;
    }
}
