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

// using cache, if provided in Configuration
// otherwise won't remember
$router->remember(function () use ($router) {
    $manager = ( new BlueprintManager() );

    $manager
        // setting namespace for all route actions that are strings and not callables
        ->namespace('Gzhegow\Router\Tests\Classes')
        // setting cors headers for all routes in the group
        ->cors(function (CorsBuilder $cors) {
            
            return $cors
            ->allowCredentials(true)
            ->allowOrigins([ 'https://test\.(.+)\.loc' ])
            ->allowHeaders([ 'Authorization', 'X-(.+)' ])
            ->exposeHeaders([ 'X-(.+)' ]);
        })
        // using loader to get routes, for example: \Closure function or filepath or directory path
        ->group(function () use ($manager) {
            $manager
                ->group(function () use ($manager) {
                    $manager->get('/login', 'TestUserController@login')->name('login');
                    $manager->post('/login', 'TestUserController@loginPost')->name('loginPost');
                });

            $manager
                // adding name prefix to all routes in the group
                ->name('users.')
                // adding endpoint prefix to all routes in the group
                ->endpoint('/users')
                // adding middlewares for all routes in the group
                ->middlewares([ '@api' ])
                ->group(function () use ($manager) {
                    $manager->get('/', 'TestUserController@index')->name('index');
                    $manager->post('/', 'TestUserController@post')->name('post');

                    $manager->get('/{id}', 'TestUserController@get')->name('get');
                    $manager->put('/{id}', 'TestUserController@put')->name('put');
                    $manager->delete('/{id}', 'TestUserController@delete')->name('delete');
                });
        });

    // using loader to load our custom manager, you can write your own manager for that
    // also you can write annotation reader to read controller classes directly
    $router->load($manager);

    // return main route collection to be cached
    return $router->getRouteCollection();
});

// you can set custom httpMethod(), for example `CLI` to work with console commands, by default it gets method
// 1. from $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']
// 2. from $_SERVER['HTTP_METHOD']
$routeSpecification = ( new HttpRouteSpecification() )
    ->urlAddress('/users/1')
    // ->httpMethod('GET')
    ;

// using Specification pattern to search routes inside collection
$route = $router->match($routeSpecification);

// setting custom bindings (usually it works inside middlewares where you get some AR-models from database)
$route->addBindings([ 'key' => 'value' ]);

// you can pass any arguments, like ServerRequestInterface or maybe ConsoleInput
// container will autowire each action using arguments, bindings and container items
// as you remember you can put your own PsrContainer inside configuration, and router will use both
$arguments = [];
$result = $router->handle($route, ...$arguments);

// ...

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
 *     'maxAge' => NULL,
 *   ]),
 * ]);
 */
``` 

## Todo

1. (?) CLI compiler like Laravel
2. (?) Several separators for each route type? Overengineering
