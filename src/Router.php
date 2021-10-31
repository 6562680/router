<?php


namespace Gzhegow\Router;

use Gzhegow\Router\Domain\Cors\Cors;
use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Domain\Route\RouteCollection;
use Gzhegow\Router\Domain\Configuration\Configuration;
use Gzhegow\Router\Domain\Handler\Action\GenericAction;
use Gzhegow\Router\Domain\Configuration\PatternCollection;
use Gzhegow\Router\Domain\Configuration\MiddlewareCollection;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;
use Gzhegow\Router\Domain\Handler\Middleware\GenericMiddleware;
use Gzhegow\Router\Domain\Route\Specification\RouteSpecificationInterface;


/**
 * Router
 */
class Router implements RouterInterface
{
    /**
     * @var RouterContainerInterface
     */
    protected $routerContainer;

    /**
     * @var RouteCollection
     */
    protected $routeCollection;

    /**
     * @var Route
     */
    protected $routeCurrent;
    /**
     * @var Cors
     */
    protected $corsCurrent;


    /**
     * Constructor
     *
     * @param null|Configuration $configuration
     */
    public function __construct(?Configuration $configuration)
    {
        $routerContainer = new RouterContainer($configuration);

        $this->routerContainer = $routerContainer;

        $this->routeCollection = $routerContainer->getRouteCollection();
    }


    /**
     * @param mixed $source
     *
     * @return static
     */
    public function load($source)
    {
        $loader = $this->routerContainer->getRouteLoader();

        if (! $loader->supportsSource($source)) {
            throw new InvalidArgumentException(
                [ 'Invalid source: %s', $source ]
            );
        }

        $routeCollection = $loader->loadSource($source, $loader);

        if ($routes = $routeCollection->getRoutes()) {
            $compiler = $this->routerContainer->getRouteCompiler();

            foreach ( $routes as $route ) {
                if ($compiler->supportsRoute($route)) {
                    $compiler->compileRoute($route);
                }
            }
        }

        $this->routeCollection->merge($routeCollection);

        return $this;
    }


    /**
     * @param Route $route
     * @param mixed ...$arguments
     *
     * @return null|int|mixed
     */
    public function handle(Route $route, ...$arguments)
    {
        $this->setRouteCurrent($route);

        if ($cors = $this->routeCollection->hasCorsFor($route)) {
            $this->setCorsCurrent($cors);
        }

        $result = $this->handleRoute($route, ...$arguments);

        $this->corsCurrent = null;
        $this->routerContainer->unset(Cors::class);

        $this->routeCurrent = null;
        $this->routerContainer->unset(Route::class);

        return $result;
    }

    /**
     * @param Route $route
     * @param mixed ...$arguments
     *
     * @return null|int|mixed
     */
    protected function handleRoute(Route $route, ...$arguments)
    {
        $action = new GenericAction($route->getAction(),
            $this->routerContainer->getActionProcessor()
        );

        $middlewares = [];
        if ($routeMiddlewares = $route->getMiddlewares()) {
            $middlewareCollection = $this->routerContainer->getMiddlewareCollection();

            foreach ( $routeMiddlewares as $routeMiddleware ) {
                $array = null
                    ?? $middlewareCollection->hasMiddlewareGroup($routeMiddleware)
                    ?? [ $routeMiddleware ];

                $middlewares = array_merge($middlewares, $array);
            }

            end($middlewares);
            while ( $middleware = current($middlewares) ) {
                $middleware = new GenericMiddleware($middleware,
                    $this->routerContainer->getActionProcessor()
                );

                $middleware->setNext($action);

                $action = $middleware;

                prev($middlewares);
            }
        }

        $result = $action->handle(...$arguments);

        return $result;
    }


    /**
     * @return RouterContainerInterface
     */
    public function getRouterContainer() : RouterContainerInterface
    {
        return $this->routerContainer;
    }

    /**
     * @return RouterCacheInterface
     */
    public function getRouterCache() : RouterCacheInterface
    {
        return $this->routerContainer->getRouterCache();
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
     * @return null|Cors
     */
    public function getCorsCurrent() : ?Cors
    {
        return $this->corsCurrent;
    }


    /**
     * @return MiddlewareCollection
     */
    public function getMiddlewareCollection() : MiddlewareCollection
    {
        return $this->routerContainer->getMiddlewareCollection();
    }

    /**
     * @return PatternCollection
     */
    public function getPatternCollection() : PatternCollection
    {
        return $this->routerContainer->getPatternCollection();
    }


    /**
     * @param RouteCollection $routeCollection
     *
     * @return static
     */
    protected function setRouteCollection(RouteCollection $routeCollection)
    {
        $this->routeCollection = $routeCollection;
        $this->routerContainer->set(RouteCollection::class, $routeCollection);

        return $this;
    }


    /**
     * @param Route $route
     *
     * @return static
     */
    protected function setRouteCurrent(Route $route)
    {
        $this->routeCurrent = $route;
        $this->routerContainer->set(Route::class, $route);

        return $this;
    }

    /**
     * @param Cors $cors
     *
     * @return static
     */
    protected function setCorsCurrent(Cors $cors)
    {
        $this->corsCurrent = $cors;
        $this->routerContainer->set(Cors::class, $cors);

        return $this;
    }


    /**
     * @param null|RouteSpecificationInterface $routeSpecification
     * @param null|int                         $limit
     * @param null|int                         $offset
     *
     * @return Route[]
     */
    public function matchAll(?RouteSpecificationInterface $routeSpecification, int $limit = null, int $offset = null) : array
    {
        $routes = $this->routeCollection->all($routeSpecification, $limit, $offset);

        return $routes;
    }

    /**
     * @param null|RouteSpecificationInterface $routeSpecification
     * @param null|int                         $offset
     *
     * @return null|Route
     */
    public function match(?RouteSpecificationInterface $routeSpecification, int $offset = null) : ?Route
    {
        $route = $this->routeCollection->first($routeSpecification, $offset);

        return $route;
    }


    /**
     * @param \Closure    $closure
     * @param null|int    $ttl
     * @param null|string $key
     *
     * @return static
     */
    public function remember(\Closure $closure, int $ttl = null, string $key = null)
    {
        $cache = $this->routerContainer->getRouterCache();

        $routeCollection = $cache->remember($closure, $ttl, $key);

        $this->setRouteCollection($routeCollection);

        return $this;
    }
}
