# Router

Well polymorphed router library

## How to use

```php
use Gzhegow\Router\Router;
use Gzhegow\Router\Domain\Cors\CorsBuilder;
use Gzhegow\Router\Tests\Classes\TestMiddleware;
use Gzhegow\Router\Domain\Blueprint\BlueprintManager;
use Gzhegow\Router\Domain\Configuration\Configuration;
use Gzhegow\Router\Domain\Route\Specification\HttpRouteSpecification;


require_once __DIR__ . '/vendor/autoload.php';


$configuration = ( new Configuration() )
    // ->setContainer(null) // \Psr\Container\ContainerInterface
    // ->setCache(null) // Psr\SimpleCache\CacheInterface|callable
    // ->setRouteCompiler(null) // \Gzhegow\Router\Service\RouteCompiler\RouteCompilerInterface|callable
    // ->setRouteLoader(null) // \Gzhegow\Router\Service\RouteLoader\RouteLoaderInterface|callable
    // ->setActionProcessor(null) // \Gzhegow\Router\Service\ActionProcessor\ActionProcessorInterface|callable
    // ->setRouteCollection(null) // \Gzhegow\Router\Domain\Route\RouteCollection|callable
    // ->setMiddlewareCollection(null) // \Gzhegow\Router\Domain\Configuration\MiddlewareCollection|callable
    // ->setPatternCollection(null) // \Gzhegow\Router\Domain\Configuration\PatternCollection|callable    
;

$router = new Router($configuration);

// adding custom middleware aliases and middleware groups
$router
    ->getMiddlewareCollection()
    ->addMiddlewareAlias('test', TestMiddleware::class)
    ->addMiddlewareGroup('@api', [])
    ->addMiddlewareGroup('@cli', [
        TestMiddleware::class,
    ])
    ->addMiddlewareGroup('@web', [
        'test', // using previously declared alias,
    ]);

// adding custom wildcards to use inside routes
$router
    ->getPatternCollection()
    ->addPattern('*', '.+')
    ->addPattern('id', '[0-9]+')
    ->addPattern('controller', '(?:[^\/]+\/)+(?=[^\/]+)')
    ->addPattern('action', '[^\/]+(?=|$)');

// using cache, if provided in Configuration
// otherwise won't remember
$router->remember(function () use ($router) {
    $manager = new BlueprintManager();

    $manager
        // setting namespace for all route actions that are strings and not callables
        ->namespace('Gzhegow\Router\Tests\Classes')
        ->group(function () use ($manager) {
            $manager
                ->middlewares([ '@api' ])
                ->name('api.')
                ->endpoint('api')
                // setting cors headers for all routes in the group
                ->cors(function (CorsBuilder $cors) {
                    $cors
                        ->allowCredentials(true)
                        ->allowOrigins([ 'https:\/\/(.+)\.test\.loc' ])
                        ->allowHeaders([ 'Authorization', 'X-(.+)' ])
                        ->exposeHeaders([ 'X-(.+)' ]);
                })
                // using loader to get routes, for example: \Closure function or filepath or directory path
                ->group(function () use ($manager) {
                    $manager
                        ->name('users.')
                        ->endpoint('/users')
                        ->group(function () use ($manager) {
                            $manager->get('', 'TestUserController@index')->name('index');
                            $manager->post('', 'TestUserController@post')->name('post');

                            $manager->get('{id}', 'TestUserController@get')->name('get');
                            $manager->put('{id}', 'TestUserController@put')->name('put');
                            $manager->delete('{id}', 'TestUserController@delete')->name('delete');
                        });
                });

            $manager
                ->middlewares([ '@cli' ])
                ->name('cli.')
                ->group(function () use ($manager) {
                    $manager
                        ->name('users.')
                        ->group(function () use ($manager) {
                            $manager->cli([
                                'users:dump',
                                '{--users+ > Comma separated list of user ids}',
                            ], 'TestUserController@dump')->name('dump');

                            $manager->cli([
                                'users:load',
                                '{--force|f > Forces non-interactive mode}',
                            ], 'TestUserController@load')->name('load');
                        });
                });

            $manager
                ->middlewares([ '@web' ])
                ->name('web.')
                ->group(function () use ($manager) {
                    $manager
                        ->name('users.')
                        ->group(function () use ($manager) {
                            $manager->get('login', 'TestUserController@login')->name('login');
                            $manager->post('login', 'TestUserController@loginPost')->name('loginPost');
                        });
                });
        });

    $router->load($manager);

    return $router->getRouteCollection();
});

// you can set custom httpMethod(), for example `CLI` to work with console commands, by default it gets method
// 1. from $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']
// 2. from $_SERVER['HTTP_METHOD']
$routeSpecification = ( new HttpRouteSpecification() )
    ->httpMethod('GET')
    ->urlAddress('/api/users/1');

// using Specification pattern to search routes inside collection
$route = $router->match($routeSpecification);

// setting custom bindings
// usually it works inside middlewares where you get some AR-models from database
$route->addBindings([ 'key' => 'value' ]);

// you can pass any arguments, like ServerRequestInterface or maybe ConsoleInput
// container will autowire each action using arguments, bindings and container items
// as you remember you can put your own PsrContainer inside configuration, and router will use both
$arguments = [];
$result = $router->handle($route, ...$arguments);

// ...

var_export($route);
/**
 * Gzhegow\Router\Domain\Route\Route::__set_state(array(
 *    'method' => 'GET',
 *    'action' => 'Gzhegow\\Router\\Tests\\Classes\\TestUserController@get',
 *    'endpoint' => Gzhegow\Router\Domain\Endpoint\Endpoint::__set_state(array(
 *      'value' => 'api/users/{id}',
 *      'regex' => '/^api\\/users\\/(?P<id>[0-9]+)$/u',
 *    )),
 *    'signature' => NULL,
 *    'name' => 'api.users.get',
 *    'description' => NULL,
 *    'bindings' => NULL,
 *    'middlewares' => array (
 *      '@api' => '@api',
 *    ),
 *    'tags' => NULL,
 * ));
 */
```
