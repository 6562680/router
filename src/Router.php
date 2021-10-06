<?php


namespace Gzhegow\Router;

use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Domain\Route\RouteCollection;
use Gzhegow\Router\Domain\Configuration\Configuration;
use Gzhegow\Router\Domain\Configuration\PatternCollection;
use Gzhegow\Router\Domain\Configuration\MiddlewareCollection;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;
use Gzhegow\Router\Domain\Route\Specification\RouteSpectificationInterface;


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
     * Constructor
     *
     * @param null|Configuration $configuration
     */
    public function __construct(?Configuration $configuration)
    {
        $routerContainer = new RouterContainer($configuration);

        $routerContainer->set(RouterInterface::class, $this);

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

        $routeCollection = $loader->loadSource($source);

        $this->routeCollection = $routeCollection;

        $this->routerContainer->set(RouteCollection::class, $routeCollection);

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
        $factory = $this->routerContainer->getRouterFactory();

        $this->routeCurrent = $route;
        $this->routerContainer->set(Route::class, $route);

        $handler = $factory->newAction($route->getAction());

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
                $handler = $factory->newMiddleware($middleware, $handler);

                prev($middlewares);
            }
        }

        $result = $handler->handle(...$arguments);

        $this->routerContainer->unset(Route::class);
        $this->routeCurrent = null;

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
        return $this->routerContainer->getRouteCollection();
    }

    /**
     * @return null|Route
     */
    public function getRouteCurrent() : ?Route
    {
        return $this->routeCurrent;
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

        $this->routeCollection = $routeCollection;

        $this->routerContainer->set(RouteCollection::class, $routeCollection);

        return $this;
    }


    /**
     * @param null|RouteSpectificationInterface $routeSpecification
     * @param null|int                          $limit
     * @param null|int                          $offset
     *
     * @return Route[]
     */
    public function matchAll(RouteSpectificationInterface $routeSpecification = null, int $limit = null, int $offset = null) : array
    {
        $route = $this->getRouteCollection()->all($routeSpecification);

        return $route;
    }

    /**
     * @param null|RouteSpectificationInterface $routeSpecification
     * @param null|int                          $offset
     *
     * @return null|Route
     */
    public function match(RouteSpectificationInterface $routeSpecification = null, int $offset = null) : ?Route
    {
        $route = $this->getRouteCollection()->first($routeSpecification);

        return $route;
    }
}
