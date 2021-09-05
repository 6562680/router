<?php


namespace Gzhegow\Router;

use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Domain\Route\BlueprintRoute;
use Gzhegow\Router\Domain\Route\RouteCollection;
use Gzhegow\Router\Domain\Configuration\Configuration;
use Gzhegow\Router\Domain\Route\RouteCollectionBuilder;
use Gzhegow\Router\Domain\Specification\SpecificationInterface;


/**
 * Router
 */
class Router
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var RouteCollection
     */
    protected $routeCollection;
    /**
     * @var Route
     */
    protected $routeCurrent;


    /**
     * Constructor
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        $this->routeCollection = new RouteCollection();
        $this->routeCurrent = null;
    }


    /**
     * @param Route      $route
     * @param null|mixed $payload
     * @param mixed      ...$arguments
     *
     * @return null|int|mixed
     */
    public function handle(Route $route, $payload = null, ...$arguments)
    {
        $this->routeCurrent = $route;

        $result = $payload;

        if ($middlewares = $route->getMiddlewares()) {
            $middlewareProcessor = $this->configuration->getMiddlewareProcessor();

            foreach ( $middlewares as $middleware ) {
                $result = $middlewareProcessor->processMiddleware($middleware, null, $result, ...$arguments);
            }
        }

        $actionProcessor = $this->configuration->getActionProcessor();

        $result = $actionProcessor->processAction($route, $result, ...$arguments);

        $this->routeCurrent = null;

        return $result;
    }


    /**
     * @return Configuration
     */
    public function getConfiguration() : Configuration
    {
        return $this->configuration;
    }


    /**
     * @return RouteCollection
     */
    public function getRouteCollection() : RouteCollection
    {
        return $this->routeCollection;
    }

    /**
     * @return null|Route
     */
    public function getRouteCurrent() : ?Route
    {
        return $this->routeCurrent;
    }


    /**
     * @param Route $route
     *
     * @return static
     */
    public function addRoute(Route $route)
    {
        $this->routeCollection->addRoute($route);

        return $this;
    }

    /**
     * @param BlueprintRoute $routeBlueprint
     *
     * @return static
     */
    public function addRouteBlueprint(BlueprintRoute $routeBlueprint)
    {
        $route = $this->configuration->getRouteCompiler()->compile($routeBlueprint);

        $this->routeCollection->addRoute($route);

        return $this;
    }


    /**
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function get(string $endpoint, $action) : BlueprintRoute
    {
        $routeCollectionBuilder = $this->configuration->getRouteCollectionBuilder();

        $route = $routeCollectionBuilder->get($endpoint, $action);

        return $route;
    }

    /**
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function post(string $endpoint, $action) : BlueprintRoute
    {
        $routeCollectionBuilder = $this->configuration->getRouteCollectionBuilder();

        $route = $routeCollectionBuilder->post($endpoint, $action);

        return $route;
    }

    /**
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function put(string $endpoint, $action) : BlueprintRoute
    {
        $routeCollectionBuilder = $this->configuration->getRouteCollectionBuilder();

        $route = $routeCollectionBuilder->put($endpoint, $action);

        return $route;
    }

    /**
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function patch(string $endpoint, $action) : BlueprintRoute
    {
        $routeCollectionBuilder = $this->configuration->getRouteCollectionBuilder();

        $route = $routeCollectionBuilder->patch($endpoint, $action);

        return $route;
    }

    /**
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function delete(string $endpoint, $action) : BlueprintRoute
    {
        $routeCollectionBuilder = $this->configuration->getRouteCollectionBuilder();

        $route = $routeCollectionBuilder->delete($endpoint, $action);

        return $route;
    }

    /**
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function purge(string $endpoint, $action) : BlueprintRoute
    {
        $routeCollectionBuilder = $this->configuration->getRouteCollectionBuilder();

        $route = $routeCollectionBuilder->purge($endpoint, $action);

        return $route;
    }


    /**
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function options(string $endpoint, $action) : BlueprintRoute
    {
        $routeCollectionBuilder = $this->configuration->getRouteCollectionBuilder();

        $route = $routeCollectionBuilder->options($endpoint, $action);

        return $route;
    }


    /**
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function cli(string $endpoint, $action) : BlueprintRoute
    {
        $routeCollectionBuilder = $this->configuration->getRouteCollectionBuilder();

        $route = $routeCollectionBuilder->cli($endpoint, $action);

        return $route;
    }


    /**
     * @param string $method
     * @param string $endpoint
     * @param mixed  $action
     *
     * @return BlueprintRoute
     */
    public function route(string $method, string $endpoint, $action) : BlueprintRoute
    {
        $routeCollectionBuilder = $this->configuration->getRouteCollectionBuilder();

        $routeBlueprint = $routeCollectionBuilder->route($method, $endpoint, $action);

        return $routeBlueprint;
    }

    /**
     * @param string|callable|object|mixed $groupLoader
     *
     * @return static
     */
    public function group($groupLoader)
    {
        $builder = $this->configuration->getRouteCollectionBuilder()->group($groupLoader);

        $routeCollection = $builder->build();

        foreach ( $routeCollection->getRoutes() as $route ) {
            $this->routeCollection->addRoute($route);
        }

        return $this;
    }


    /**
     * @param string $endpoint
     *
     * @return RouteCollectionBuilder
     */
    public function endpoint(string $endpoint) : RouteCollectionBuilder
    {
        $routeCollectionBuilder = $this->configuration->getRouteCollectionBuilder();

        $builder = $routeCollectionBuilder->endpoint($endpoint);

        return $builder;
    }


    /**
     * @param string $namespace
     *
     * @return RouteCollectionBuilder
     */
    public function namespace(string $namespace) : RouteCollectionBuilder
    {
        $routeCollectionBuilder = $this->configuration->getRouteCollectionBuilder();

        $builder = $routeCollectionBuilder->name($namespace);

        return $builder;
    }

    /**
     * @param string $name
     *
     * @return RouteCollectionBuilder
     */
    public function name(string $name) : RouteCollectionBuilder
    {
        $routeCollectionBuilder = $this->configuration->getRouteCollectionBuilder();

        $builder = $routeCollectionBuilder->name($name);

        return $builder;
    }


    /**
     * @param array $bindings
     *
     * @return RouteCollectionBuilder
     */
    public function bindings(array $bindings) : RouteCollectionBuilder
    {
        $routeCollectionBuilder = $this->configuration->getRouteCollectionBuilder();

        $builder = $routeCollectionBuilder->bindings($bindings);

        return $builder;
    }

    /**
     * @param array $tags
     *
     * @return RouteCollectionBuilder
     */
    public function tags(array $tags) : RouteCollectionBuilder
    {
        $routeCollectionBuilder = $this->configuration->getRouteCollectionBuilder();

        $builder = $routeCollectionBuilder->tags($tags);

        return $builder;
    }

    /**
     * @param array $middlewares
     *
     * @return RouteCollectionBuilder
     */
    public function middlewares(array $middlewares) : RouteCollectionBuilder
    {
        $routeCollectionBuilder = $this->configuration->getRouteCollectionBuilder();

        $builder = $routeCollectionBuilder->middlewares($middlewares);

        return $builder;
    }


    /**
     * @param SpecificationInterface $routeSpec
     *
     * @return null|Route
     */
    public function match(SpecificationInterface $routeSpec) : ?Route
    {
        if ($route = $this->routeCollection->findBySpec($routeSpec)) {
            $this->routeCurrent = $route;
        }

        return $route;
    }
}
