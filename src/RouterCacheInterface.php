<?php

namespace Gzhegow\Router;


use Gzhegow\Router\Domain\Route\RouteCollection;


/**
 * RouterCache
 */
interface RouterCacheInterface
{
    /**
     * @param \Closure $func
     * @param null|int $ttl
     * @param null|int $key
     *
     * @return RouteCollection
     */
    public function remember(\Closure $func, int $ttl = null, int $key = null) : RouteCollection;
}
