<?php


namespace Gzhegow\Router\Tests\Classes;


/**
 * TestMiddleware
 */
class TestMiddleware
{
    public function __invoke()
    {
        dump(__METHOD__);
    }
}
