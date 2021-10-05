<?php


namespace Gzhegow\Router\Domain\Route;

use Gzhegow\Router\Exceptions\Runtime\OverflowException;
use Gzhegow\Router\Domain\Route\Specification\RouteSpectificationInterface;


/**
 * RouteCollection
 */
class RouteCollection
{
    /**
     * @var Route[]
     */
    protected $routes = [];
    /**
     * @var bool[][]
     */
    protected $routesUniq = [
        'method.endpoint' => [],
        'name'            => [],
    ];


    /**
     * @return array
     */
    public function __serialize() : array
    {
        return [
            'routes'     => $this->routes,
            'routesUniq' => $this->routesUniq,
        ];
    }

    /**
     * @param array $data
     */
    public function __unserialize(array $data) : void
    {
        $this->routes = $data[ 'routes' ] ?? [];
        $this->routesUniq = $data[ 'routesUniq' ]
            ?? [
                'method.endpoint' => [],
                'name'            => [],
            ];
    }


    /**
     * @return Route[]
     */
    public function getRoutes() : array
    {
        return $this->routes;
    }


    /**
     * @param Route|Route[] $routes
     *
     * @return static
     */
    public function setRoutes($routes)
    {
        $this->routes = [];

        $this->addRoutes($routes);

        return $this;
    }

    /**
     * @param Route|Route[] $routes
     *
     * @return static
     */
    public function addRoutes($routes)
    {
        $routes = is_iterable($routes)
            ? $routes
            : [ $routes ];

        foreach ( $routes as $route ) {
            $this->addRoute($route);
        }

        return $this;
    }

    /**
     * @param Route $route
     *
     * @return static
     */
    public function addRoute(Route $route)
    {
        $routeMethod = $route->getMethod();
        $routeEndpoint = $route->getEndpoint();
        $routeName = $route->getName();

        $uniqMethodEndpoint = $routeMethod . '.' . $routeEndpoint;
        $uniqName = $routeName;

        if (isset($this->routesUniq[ 'method.endpoint' ][ $uniqMethodEndpoint ])) {
            throw new OverflowException(
                [ 'Blueprint is already exists by Method/Endpoint: %s', $uniqMethodEndpoint ]
            );
        }

        if (null !== $uniqName) {
            if (isset($this->routesUniq[ 'name' ][ $uniqName ])) {
                throw new OverflowException(
                    [ 'Blueprint is already exists by Name: %s', $uniqName ]
                );
            }
        }

        $this->routes[] = $route;

        end($this->routes);
        $idx = key($this->routes);

        $this->routesUniq[ 'method.endpoint' ][ $uniqMethodEndpoint ] = $idx;

        if (null !== $uniqName) {
            $this->routesUniq[ 'name' ][ $uniqName ] = $idx;
        }

        return $this;
    }


    /**
     * @param null|RouteSpectificationInterface $routeSpecification
     * @param null|int                          $limit
     * @param null|int                          $offset
     *
     * @return Route[]
     */
    public function all(RouteSpectificationInterface $routeSpecification = null, int $limit = null, int $offset = null) : array
    {
        $offset = $offset ?? 0;

        $matches = [];

        foreach ( $this->routes as $idx => $route ) {
            if ($offset-- > 0) continue;

            if (! $routeSpecification
                || $routeSpecification->isMatch($route)
            ) {
                $matches[ $idx ] = $route;
            }

            if (--$limit <= 0) break;
        }

        return $matches;
    }

    /**
     * @param null|RouteSpectificationInterface $routeSpecification
     * @param null|int                          $offset
     *
     * @return null|Route
     */
    public function first(RouteSpectificationInterface $routeSpecification = null, int $offset = null) : ?Route
    {
        $items = $this->all($routeSpecification, 1, $offset);

        $match = null !== key($items)
            ? reset($items)
            : null;

        return $match;
    }
}
