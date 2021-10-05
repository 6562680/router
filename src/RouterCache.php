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
            $routeCollection = null;

            if (! ( $this->cache && $this->cache->has($key) )) {
                $routeCollection = $this->call($func);
            }

            if ($this->cache) {
                $this->cache->set($key, $routeCollection, $ttl);
            }
        }
        catch ( \Psr\SimpleCache\InvalidArgumentException $e ) {
            throw new RuntimeException($e->getMessage(), null, $e);
        }

        return $routeCollection;
    }


    /**
     * @param \Closure $func
     *
     * @return RouteCollection
     */
    protected function call(\Closure $func) : RouteCollection
    {
        return $func();
    }
}
