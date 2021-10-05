<?php

use Gzhegow\Router\Router;
use Psr\Container\ContainerInterface;
use Gzhegow\Router\Domain\Cors\CorsBuilder;
use Gzhegow\Router\Tests\Classes\TestMiddleware;
use Gzhegow\Router\Domain\Blueprint\BlueprintManager;
use Gzhegow\Router\Domain\Configuration\Configuration;
use Gzhegow\Router\Domain\Route\Specification\HttpRouteSpecification;


require_once __DIR__ . '/vendor/autoload.php';


\Symfony\Component\VarDumper\VarDumper::setHandler(function ($val) {
    $cloner = new \Symfony\Component\VarDumper\Cloner\VarCloner([
        ContainerInterface::class => function ($var) {
            return is_null($var);
        },
    ]);

    $dumper = new \Symfony\Component\VarDumper\Dumper\CliDumper();

    return $dumper->dump($cloner->cloneVar($val));
});


$configuration = ( new Configuration() )
    ->setCache(null)
    ->setContainer(null)
    ->setRouterFactory(null)
    ->setRouteCompiler(null)
    ->setRouteLoader(null)
    ->setActionProcessor(null)
    ->setCorsMiddleware(null);

$router = new Router($configuration);

// adding custom middleware aliases and middleware groups
$router
    ->getMiddlewareCollection()
    ->addMiddlewareAlias('test', TestMiddleware::class)
    ->addMiddlewareGroup('@api', [
        'test', // using previously declared alias
    ])
    ->addMiddlewareGroup('@web', [
        TestMiddleware::class, // using another `action`
    ]);

// adding custom wildcards to use inside routes
$router
    ->getPatternCollection()
    ->addPattern('*', '.+')
    ->addPattern('id', '[0-9]+')
    ->addPattern('controller', '(?:[^\/]+\/)+(?=[^\/]+)')
    ->addPattern('action', '[^\/]+(?=|$)');

// using cache if provided otherwise won't remember
$router->remember(function () use ($router) {
    $manager = ( new BlueprintManager() );

    $manager
        ->namespace('Gzhegow\Router\Tests\Classes')
        ->cors(function (CorsBuilder $cors) {
            $cors
                ->allowCredentials(true)
                ->allowOrigins([ 'https:\/\/(.+)\.test\.loc' ])
                ->allowHeaders([ 'Authorization', 'X-(.+)' ])
                ->exposeHeaders([ 'X-(.+)' ]);
        })
        ->group(function () use ($manager) {
            $manager
                ->middlewares([ '@web' ])
                ->group(function () use ($manager) {
                    $manager->get('/login', 'TestUserController@login')->name('login');
                    $manager->post('/login', 'TestUserController@loginPost')->name('loginPost');
                });

            $manager
                ->name('users.')
                ->endpoint('/users')
                ->middlewares([ '@api' ])
                ->group(function () use ($manager) {
                    $manager->get('/', 'TestUserController@index')->name('index');
                    $manager->post('/', 'TestUserController@post')->name('post');

                    $manager->get('/{id}', 'TestUserController@get')->name('get');
                    $manager->put('/{id}', 'TestUserController@put')->name('put');
                    $manager->delete('/{id}', 'TestUserController@delete')->name('delete');
                });
        });

    $router->load($manager);

    return $router->getRouteCollection();
});

$routeSpecification = ( new HttpRouteSpecification() )
    ->httpMethod('GET')
    ->urlAddress('/users/1');

$route = $router->match($routeSpecification);

dd($route);
dd($router->getRouteCollection());
var_export($route);

/**
 * Gzhegow\Router\Domain\Route\Route::__set_state([
 *   'method' => 'GET',
 *   'action' => 'Gzhegow\\Router\\Tests\\Classes\\TestUserController@index',
 *   'endpoint' => '/users/',
 *   'endpointRegex' => '/users/',
 *   'name' => 'users.index',
 *   'description' => NULL,
 *   'bindings' => [],
 *   'middlewares' => [
 *     'Gzhegow\\Router\\Domain\\Cors\\CorsMiddleware' => 'Gzhegow\\Router\\Domain\\Cors\\CorsMiddleware',
 *     '@api' => '@api',
 *   ],
 *   'tags' => [],
 *   'cors' => Gzhegow\Router\Domain\Cors\Cors::__set_state([
 *     'allowOrigins' => NULL,
 *     'allowHeaders' => NULL,
 *     'exposeHeaders' => NULL,
 *     'allowCredentials' => NULL,
 *     'maxAge' => 3600,
 *   ]),
 * ]);
 */
