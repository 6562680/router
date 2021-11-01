<?php


namespace Gzhegow\Router\Tests\Classes;

use Gzhegow\Router\Domain\Cors\Cors;
use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Domain\Handler\HandlerInterface;


/**
 * TestMiddleware
 */
class TestMiddleware
{
    public function __invoke(HandlerInterface $next, Route $route, Cors $cors = null, ...$arguments)
    {
        print_r(__METHOD__ . PHP_EOL);
        print_r([ $route, $cors ]);
        print_r(count($arguments) . PHP_EOL);

        return $next->handle(...$arguments);
    }
}
