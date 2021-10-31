<?php


namespace Gzhegow\Router\Tests\Classes;


use Gzhegow\Router\Domain\Cors\Cors;
use Gzhegow\Router\Domain\Route\Route;


/**
 * TestUserController
 */
class TestUserController
{
    public function get(Route $route, Cors $cors = null, ...$arguments)
    {
        print_r(__METHOD__ . PHP_EOL);
        print_r([ $route, $cors ]);
        print_r($arguments);

        return 1;
    }

    public function post(Route $route, Cors $cors = null, ...$arguments)
    {
        print_r(__METHOD__ . PHP_EOL);
        print_r([ $route, $cors ]);
        print_r($arguments);

        return 1;
    }

    public function put(Route $route, Cors $cors = null, ...$arguments)
    {
        print_r(__METHOD__ . PHP_EOL);
        print_r([ $route, $cors ]);
        print_r($arguments);

        return 1;
    }

    public function delete(Route $route, Cors $cors = null, ...$arguments)
    {
        print_r(__METHOD__ . PHP_EOL);
        print_r([ $route, $cors ]);
        print_r($arguments);

        return 1;
    }


    public function index(Route $route, Cors $cors = null, ...$arguments)
    {
        print_r(__METHOD__ . PHP_EOL);
        print_r([ $route, $cors ]);
        print_r($arguments);

        return 1;
    }


    public function login(Route $route, Cors $cors = null, ...$arguments)
    {
        print_r(__METHOD__ . PHP_EOL);
        print_r([ $route, $cors ]);
        print_r($arguments);

        return 1;
    }

    public function loginPost(Route $route, Cors $cors = null, ...$arguments)
    {
        print_r(__METHOD__ . PHP_EOL);
        print_r([ $route, $cors ]);
        print_r($arguments);

        return 1;
    }


    public function exec(Route $route, Cors $cors = null, ...$arguments)
    {
        print_r(__METHOD__ . PHP_EOL);
        print_r([ $route, $cors ]);
        print_r($arguments);

        return 1;
    }

    public function dump(Route $route, Cors $cors = null, ...$arguments)
    {
        print_r(__METHOD__ . PHP_EOL);
        print_r([ $route, $cors ]);
        print_r($arguments);

        return 1;
    }
}
