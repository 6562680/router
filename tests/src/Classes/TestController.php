<?php


namespace Gzhegow\Router\Tests\Classes;


/**
 * TestController
 */
class TestController
{
    public function index($payload)
    {
        echo 'Hello, World!';

        return 1;
    }
}
