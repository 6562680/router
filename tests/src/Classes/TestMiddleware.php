<?php


namespace Gzhegow\Router\Tests\Classes;


/**
 * TestMiddleware
 */
class TestMiddleware
{
    public function handle($payload, \Closure $next)
    {
        return $next($payload);
    }
}
