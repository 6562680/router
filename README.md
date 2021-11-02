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
                            // using builder
                            $manager->cli('users:dump', 'TestUserController@dump')
                                // we use `corneltek/getoptionkit` package so before `&gt;` could be any supported signature
                                //
                                // 1) flag option (with boolean value true)
                                // d      : single character only option
                                // dir    : long option name
                                // d|dir  : short or long to `long` variable
                                //
                                // 2) value option
                                // d|dir+        : option with multiple values.
                                // d|dir:        : option require a value (MUST require)
                                // d|dir?        : option with optional value
                                // dir:=boolean  : option with type constraint of boolean
                                // dir:=date     : option with type constraint of date
                                // dir:=file     : option with type constraint of file
                                // dir:=number   : option with type constraint of number
                                // dir:=string   : option with type constraint of string
                                ->signature([
                                    0 => '{--u|users+ > Users (supports multiple values --users=1 --users=2)}',
                                    1 => '{--f|force > Forces non-interactive mode}',
                                ])
                                ->name('dump');

                            // using array (will be imploded by space and split to endpoint/signature)
                            $manager->cli([
                                0 => 'users:load',
                                1 => '{--force|f > Forces non-interactive mode}',
                            ], 'TestUserController@exec')->name('exec');

                            // using string (space is required while concatenation)
                            $manager->cli('users:do'
                                . ' ' . '{--force|f > Forces non-interactive mode}',
                                'TestUserController@do'
                            )->name('do');
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

    $routeCollection = $router->collect($manager);

    return $routeCollection;
});

// you can set custom httpMethod(), for example `CLI` to work with console commands, by default it gets method
// 1. from $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']
// 2. from $_SERVER['HTTP_METHOD']
$routeSpecification = ( new HttpRouteSpecification() )
    ->httpMethod('GET')
    ->urlAddress('api/users/1');

// using Specification pattern to search routes inside collection
$route = $router->match($routeSpecification);

// setting custom bindings
// usually it works inside middlewares where you get some AR-models from database
$route->addBindings([ 'key' => 'value' ]);

// setting custom tags
// if you want to search routes to print or export you may prefer add custom group names (`tags`) to it
$route->addTags([ 'app1', 'app2' ]);

// you can pass any arguments, like ServerRequestInterface or maybe ConsoleInput
// container will autowire each action using arguments, bindings and container items
// as you remember you can put your own PsrContainer inside configuration, and router will use both
$arguments = [];
$result = $router->handle($route, ...$arguments);

// ...

print(var_export($route, 1) . PHP_EOL);
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
 *    'description' => 'Am a description!',
 *    'bindings' => array(
 *      'id' => '1',
 *      'key' => 'value',
 *    ),
 *    'middlewares' => array(
 *      '@api' => '@api',
 *    ),
 *    'tags' => array(
 *      'app1' => true,
 *      'app2' => true,
 *    ),
 * ));
 */
```
