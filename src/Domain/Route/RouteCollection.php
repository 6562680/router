<?php


namespace Gzhegow\Router\Domain\Route;

use Gzhegow\Router\Exceptions\Runtime\OverflowException;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;
use Gzhegow\Router\Domain\Specification\SpecificationInterface;


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
     * @var array
     */
    protected $index = [
        'method'   => [],
        'endpoint' => [],
    ];
    /**
     * @var array
     */
    protected $uniq = [
        'method.endpoint' => [],
        'name'            => [],
    ];


    /**
     * @return Route[]
     */
    public function getRoutes() : array
    {
        return $this->routes;
    }


    /**
     * @param string|Route|mixed $endpoint
     *
     * @return string[]
     */
    public function getMethodsForEndpoint($endpoint) : array
    {
        $endpoint = null
            ?? ( $endpoint instanceof Route ? $endpoint->getEndpoint() : null )
            ?? $endpoint;

        if (! is_string($endpoint)) {
            throw new InvalidArgumentException(
                [ 'Invalid endpoint: %s', $endpoint ]
            );
        }

        $methods = [];
        $indexes = $this->index[ 'endpoint' ][ $endpoint ] ?? [];
        foreach ( $indexes as $idx => $bool ) {
            $methods[ $this->routes[ $idx ]->getMethod() ] = true;
        }

        $result = array_keys($methods);

        return $result;
    }


    /**
     * @param Route|Route[] $routes
     *
     * @return static
     */
    public function setRoutes($routes)
    {
        $routes = is_array($routes)
            ? $routes
            : [ $routes ];

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
        $routes = is_array($routes)
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
        $indexMethod = $routeMethod;
        $indexEndpoint = $routeEndpoint;

        if (isset($this->uniq[ 'method.endpoint' ][ $uniqMethodEndpoint ])) {
            throw new OverflowException(
                [ 'Route is already exists: %s', $uniqMethodEndpoint ]
            );
        }

        if (null !== $uniqName) {
            if (isset($this->uniq[ 'name' ][ $uniqName ])) {
                throw new OverflowException(
                    [ 'Route name should be unique: %s', $uniqName ]
                );
            }
        }

        $this->routes[] = $route;

        end($this->routes);
        $idx = key($this->routes);

        $this->uniq[ 'method.endpoint' ][ $uniqMethodEndpoint ] = $idx;

        $this->index[ 'method' ][ $indexMethod ][ $idx ] = true;
        $this->index[ 'endpoint' ][ $indexEndpoint ][ $idx ] = true;

        if (isset($uniqName)) {
            $this->uniq[ 'name' ][ $uniqName ] = $idx;
        }

        return $this;
    }


    /**
     * @param SpecificationInterface $routeSpec
     *
     * @return null|Route
     */
    public function findBySpec(SpecificationInterface $routeSpec) : ?Route
    {
        foreach ( $this->routes as $route ) {
            if ($routeSpec->isMatch($route)) {
                return $route;
            }
        }

        return null;
    }
}
