<?php

use Gzhegow\Router\Router;
use Gzhegow\Router\Domain\Configuration\Configuration;
use Gzhegow\Router\Tests\Classes\TestMiddleware;
use Gzhegow\Router\Domain\Specification\HttpSpec;


require_once __DIR__ . '/vendor/autoload.php';

$configuration = new Configuration();
$configuration->addEndpointPattern('id', '[0-9]+');
$configuration->addMiddleware('test', TestMiddleware::class);

$router = new Router($configuration);

$router
    ->namespace('Gzhegow\Router\Tests\Classes')
    ->group(function () use ($router) {
        $router->get('/login', 'TestController@index');
        $router->post('/login', 'TestController@index');

        $router
            ->name('users')
            ->endpoint('/users')
            ->middlewares('test')
            ->group(function () use ($router) {
                $router->get('/', 'TestController@index')->name('index');
                $router->post('/', 'TestController@index')->name('store');
                $router->put('/', 'TestController@index')->name('update');
                $router->delete('/', 'TestController@index')->name('delete');
            });
    });

$routeSpec = new HttpSpec($configuration);
$routeSpec
    ->httpMethod('GET')
    ->urlAddress('/hello');

dump($router->match($routeSpec));

dd($router);
