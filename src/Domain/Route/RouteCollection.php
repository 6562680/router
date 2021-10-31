<?php


namespace Gzhegow\Router\Domain\Route;

use Gzhegow\Router\Domain\Cors\Cors;
use Gzhegow\Router\Exceptions\Runtime\OverflowException;
use Gzhegow\Router\Exceptions\Runtime\OutOfBoundsException;
use Gzhegow\Router\Domain\Route\Specification\RouteSpecificationInterface;


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
     * @var bool[][][]
     */
    protected $routesIndex;
    /**
     * @var int[][]
     */
    protected $routesUnique;

    /**
     * @var Cors[]
     */
    protected $cors = [];
    /**
     * @var int[][]
     */
    protected $corsUnique;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->routesIndex = static::$routesIndexDefault;
        $this->routesUnique = static::$routesUniqueDefault;

        $this->corsUnique = static::$corsUniqueDefault;
    }


    /**
     * @return array
     */
    public function __serialize() : array
    {
        return array_filter([
            'routes'       => $this->routes,
            'routesIndex'  => $this->routesIndex,
            'routesUnique' => $this->routesIndex,

            'cors'       => $this->cors,
            'corsUnique' => $this->corsUnique,
        ], function ($v) {
            return ! is_null($v);
        });
    }

    /**
     * @param array $data
     */
    public function __unserialize(array $data) : void
    {
        $this->routes = $data[ 'routes' ] ?? [];
        $this->routesIndex = $data[ 'routesIndex' ] ?? static::$routesIndexDefault;
        $this->routesUnique = $data[ 'routesUnique' ] ?? static::$routesUniqueDefault;

        $this->cors = $data[ 'cors' ] ?? [];
        $this->corsUnique = $data[ 'corsUnique' ] ?? static::$corsUniqueDefault;
    }


    /**
     * @return Route[]
     */
    public function getRoutes() : array
    {
        return $this->routes;
    }


    /**
     * @param Route $route
     *
     * @return Cors
     */
    public function getCorsFor(Route $route) : Cors
    {
        $cors = $this->hasCorsFor($route);

        return $cors;
    }


    /**
     * @return int[][]
     */
    public function getRoutesIndex() : array
    {
        return $this->routesIndex;
    }

    /**
     * @param string $indexName
     *
     * @return int[][]
     */
    public function getRoutesIndexByName(string $indexName) : array
    {
        if (! isset($this->routesIndex[ $indexName ])) {
            throw new OutOfBoundsException(
                [ 'No index found by name: %s', $indexName ]
            );
        }

        return $this->routesIndex[ $indexName ];
    }


    /**
     * @return int[][]
     */
    public function getRoutesUnique() : array
    {
        return $this->routesUnique;
    }

    /**
     * @param string $uniqueIndexName
     *
     * @return int[]
     */
    public function getRoutesUniqueByName(string $uniqueIndexName) : array
    {
        if (! isset($this->routesUnique[ $uniqueIndexName ])) {
            throw new OutOfBoundsException(
                [ 'No unique found by name: %s', $uniqueIndexName ]
            );
        }

        return $this->routesUnique[ $uniqueIndexName ];
    }


    /**
     * @param Route $route
     *
     * @return Cors
     */
    public function hasCorsFor(Route $route) : ?Cors
    {
        $routeMethod = $route->getMethod();
        $routeEndpoint = $route->getEndpoint();

        $uniqRouteMethodEndpoint = $routeMethod . '.' . $routeEndpoint->getValue();

        $corsIdx = $this->corsUnique[ '.route' ][ $uniqRouteMethodEndpoint ] ?? null;
        $cors = $this->cors[ $corsIdx ] ?? null;

        return $cors;
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
     * @param Route|Route[] $routes
     *
     * @return static
     */
    public function addRoutesWithCors(Cors $cors, $routes)
    {
        $routes = is_iterable($routes)
            ? $routes
            : [ $routes ];

        foreach ( $routes as $route ) {
            $this->addRouteWithCors($cors, $route);
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

        $indexMethod = $routeMethod;
        $indexEndpoint = $routeEndpoint->getValue();

        $uniqMethodEndpoint = $indexMethod . '.' . $indexEndpoint;
        $uniqName = $routeName;

        if (isset($this->routesIndex[ 'method.endpoint' ][ $uniqMethodEndpoint ])) {
            throw new OverflowException(
                [ 'Blueprint is already exists by Method/Endpoint: %s', $uniqMethodEndpoint ]
            );
        }

        if (null !== $uniqName) {
            if (isset($this->routesIndex[ 'name' ][ $uniqName ])) {
                throw new OverflowException(
                    [ 'Blueprint is already exists by Name: %s', $uniqName ]
                );
            }
        }

        $this->routes[] = $route;

        end($this->routes);
        $idx = key($this->routes);

        $this->routesIndex[ 'method' ][ $indexMethod ][ $idx ] = true;
        $this->routesIndex[ 'endpoint' ][ $indexEndpoint ][ $idx ] = true;

        $this->routesUnique[ 'method.endpoint' ][ $uniqMethodEndpoint ] = $idx;

        if (null !== $uniqName) {
            $this->routesUnique[ 'name' ][ $uniqName ] = $idx;
        }

        return $this;
    }

    /**
     * @param Cors  $cors
     * @param Route $route
     *
     * @return static
     */
    public function addRouteWithCors(Cors $cors, Route $route)
    {
        $corsArray = $cors->toArray();

        $routeMethod = $route->getMethod();
        $routeEndpoint = $route->getEndpoint();

        $uniqCorsHash = crc32(serialize(
            $corsArray
        ));

        $uniqRouteMethodEndpoint = $routeMethod . '.' . $routeEndpoint->getValue();

        if (isset($this->corsUnique[ '.route' ][ $uniqRouteMethodEndpoint ])) {
            throw new OverflowException(
                [ 'Cors is already exists by Route: %s', $uniqRouteMethodEndpoint ]
            );
        }

        $this->addRoute($route);

        $routeIdx = $this->routesUnique[ 'method.endpoint' ][ $uniqRouteMethodEndpoint ];

        $this->routesIndex[ '.cors' ][ $uniqCorsHash ][ $routeIdx ] = true;

        if (! isset($this->corsUnique[ '_hash' ][ $uniqCorsHash ])) {
            $this->cors[] = $cors;

            end($this->cors);
            $corsIdx = key($this->cors);

            $this->corsUnique[ '_hash' ][ $uniqCorsHash ] = $corsIdx;
        }

        $corsIdx = $this->corsUnique[ '_hash' ][ $uniqCorsHash ];

        $this->corsUnique[ '.route' ][ $uniqRouteMethodEndpoint ] = $corsIdx;

        return $this;
    }


    /**
     * @param RouteCollection $routeCollection
     *
     * @return static
     */
    public function merge(RouteCollection $routeCollection)
    {
        $idxMap = [];
        foreach ( $routeCollection->routes as $idx => $route ) {
            $this->routes[] = $route;

            end($this->routes);
            $idxMap[ $idx ] = key($this->routes);
        }

        foreach ( $routeCollection->routesIndex as $index => $array ) {
            foreach ( $array as $key => $list ) {
                foreach ( $list as $idx => $bool ) {
                    $this->routesIndex[ $index ][ $key ][ $idxMap[ $idx ] ] = $bool;
                }
            }
        }

        foreach ( $routeCollection->routesUnique as $index => $array ) {
            foreach ( $array as $key => $idx ) {
                if (isset($this->routesUnique[ $index ][ $key ])) {
                    throw new OverflowException(
                        [ 'Route already exists: %s', $key ]
                    );
                }

                $this->routesUnique[ $index ][ $key ] = $idxMap[ $idx ];
            }
        }

        $idxMap = [];
        foreach ( $routeCollection->cors as $idx => $cors ) {
            $this->cors[] = $cors;

            end($this->cors);
            $idxMap[ $idx ] = key($this->cors);
        }

        foreach ( $routeCollection->corsUnique as $index => $array ) {
            foreach ( $array as $key => $idx ) {
                if (isset($this->corsUnique[ $index ][ $key ])) {
                    throw new OverflowException(
                        [ 'Cors already exists: %s', $key ]
                    );
                }

                $this->corsUnique[ $index ][ $key ] = $idxMap[ $idx ];
            }
        }

        return $this;
    }


    /**
     * @param null|RouteSpecificationInterface $routeSpecification
     * @param null|int                         $limit
     * @param null|int                         $offset
     *
     * @return Route[]
     */
    public function all(RouteSpecificationInterface $routeSpecification = null, int $limit = null, int $offset = null) : array
    {
        $limit = $limit ?? INF;
        $offset = $offset ?? 0;

        $routes = null
            ?? ( $routeSpecification ? $routeSpecification->matches($this) : null )
            ?? $this->routes;

        $matches = [];
        foreach ( $routes as $idx => $route ) {
            if (! $routeSpecification || $routeSpecification->match($route)) {
                if ($offset-- > 0) continue;

                $matches[ $idx ] = $route;

                if (--$limit <= 0) break;
            }
        }

        return $matches;
    }

    /**
     * @param null|RouteSpecificationInterface $routeSpecification
     * @param null|int                         $offset
     *
     * @return null|Route
     */
    public function first(RouteSpecificationInterface $routeSpecification = null, int $offset = null) : ?Route
    {
        $items = $this->all($routeSpecification, 1, $offset);

        $match = null !== key($items)
            ? reset($items)
            : null;

        return $match;
    }


    /**
     * @var bool[][][]
     */
    protected static $routesIndexDefault = [
        'method'   => [],
        'endpoint' => [],

        '.cors' => [],
    ];
    /**
     * @var int[][]
     */
    protected static $routesUniqueDefault = [
        'method.endpoint' => [],
        'name'            => [],
    ];

    /**
     * @var int[][]
     */
    protected static $corsUniqueDefault = [
        '_hash' => [],

        '.route' => [],
    ];
}
