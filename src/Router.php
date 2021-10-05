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
        $routeCollection = new RouteCollection();
        $routerContainer = new RouterContainer($configuration);

        $routerContainer->set(RouterInterface::class, $this);
        $routerContainer->set(RouteCollection::class, $routeCollection);

        $this->routerContainer = $routerContainer;
        $this->routeCollection = $routeCollection;
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

        $loader->loadSource($source, $this->getRouteCollection());

        return $this;
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
        $factory = $this->routerContainer->getRouterFactory();

        $this->routerContainer->set(Route::class, $route);
        $this->routerContainer->set('$route', $route);

        $this->routeCurrent = $route;

        $handler = $factory->newAction($route->getAction());

        $middlewares = [];
        if ($routeMiddlewares = $route->getMiddlewares()) {
            $middlewareCollection = $this->routerContainer->getMiddlewareCollection();

            foreach ( $routeMiddlewares as $routeMiddleware ) {
                if ($middlewaresArray = $middlewareCollection->hasMiddlewareGroup($routeMiddleware)) {
                    $middlewares = array_merge($middlewares, $middlewaresArray);

                } else {
                    $middlewares[] = $routeMiddleware;
                }
            }

            end($middlewares);
            while ( $middleware = current($middlewares) ) {
                $handler = $factory->newMiddleware($middleware, $handler);

                prev($middlewares);
            }
        }

        $result = $handler->handle($payload, ...$arguments);

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

        $this->routeCollection = $cache->remember($closure, $ttl, $key);

        return $this;
    }


    /**
     * @param RouteSpectificationInterface $routeSpecification
     * @param null|int                     $limit
     * @param null|int                     $offset
     *
     * @return Route[]
     */
    public function matchAll(RouteSpectificationInterface $routeSpecification, int $limit = null, int $offset = null) : array
    {
        $route = $this->getRouteCollection()->all($routeSpecification);

        return $route;
    }

    /**
     * @param RouteSpectificationInterface $routeSpecification
     * @param null|int                     $offset
     *
     * @return null|Route
     */
    public function match(RouteSpectificationInterface $routeSpecification, int $offset = null) : ?Route
    {
        $route = $this->getRouteCollection()->first($routeSpecification);

        return $route;
    }
}
