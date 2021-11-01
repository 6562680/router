<?php


namespace Gzhegow\Router\Domain\Route\Specification;

use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Domain\Route\RouteCollection;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;
use Gzhegow\Router\Exceptions\Runtime\BadMethodCallException;


/**
 * HttpRouteSpecification
 */
class HttpRouteSpecification implements RouteSpecificationInterface
{
    /**
     * @var string
     */
    protected $httpMethod;
    /**
     * @var string
     */
    protected $urlAddress;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->httpMethod = null
            ?? $_SERVER[ 'HTTP_ACCESS_CONTROL_REQUEST_METHOD' ]
            ?? $_SERVER[ 'REQUEST_METHOD' ]
            ?? null;
    }


    /**
     * @return string
     */
    public function getHttpMethod() : string
    {
        return $this->httpMethod;
    }

    /**
     * @return string
     */
    public function getUrlAddress() : string
    {
        return $this->urlAddress;
    }


    /**
     * @param RouteCollection $routeCollection
     *
     * @return array
     */
    public function matches(RouteCollection $routeCollection) : array
    {
        if (null === $this->httpMethod) {
            throw new BadMethodCallException(
                [ 'Specification requires defined `httpMethod`: %s', $this ]
            );
        }

        if (null === $this->urlAddress) {
            throw new BadMethodCallException(
                [ 'Specification requires defined `urlAddress`: %s', $this ]
            );
        }

        $routes = [];

        $rows = $routeCollection->getRoutes();

        if ($this->httpMethod) {
            $index = $routeCollection->getRoutesIndexByName('method');

            foreach ( $index[ $this->httpMethod ] ?? [] as $id => $bool ) {
                $routes[] = $rows[ $id ];
            }

        } else {
            $routes = $rows;
        }

        return $routes;
    }

    /**
     * @param Route $route
     *
     * @return null|Route
     */
    public function match(Route $route) : ?Route
    {
        $endpoint = $route->getEndpoint();
        $endpointValue = $endpoint->getValue();
        $endpointRegex = $endpoint->getRegex();

        $matches = [];
        $isUrlAddressMatch = false
            || ( $endpointRegex && preg_match($endpointRegex, $this->urlAddress, $matches) )
            || ( $endpointValue && $endpointValue === $this->urlAddress );

        if (! $isUrlAddressMatch) {
            return null;
        }

        // ! deep clone
        $routeMatched = unserialize(serialize($route));

        $bindings = [];

        if ($matches) {
            foreach ( $matches as $idx => $value ) {
                if (is_string($idx) && $idx) {
                    $bindings[ $idx ] = $value;
                }
            }
        }

        if ($bindings) {
            $routeMatched->addBindings($bindings);
        }

        return $routeMatched;
    }


    /**
     * @param string $httpMethod
     *
     * @return static
     */
    public function httpMethod(string $httpMethod)
    {
        $httpMethod = strtoupper(trim($httpMethod));

        if (! strlen($httpMethod)) {
            throw new InvalidArgumentException(
                [ 'Invalid HttpMethod: %s', $httpMethod ]
            );
        }

        $this->httpMethod = $httpMethod;

        return $this;
    }

    /**
     * @param string $urlAddress
     *
     * @return static
     */
    public function urlAddress(string $urlAddress)
    {
        $urlAddress = trim($urlAddress);

        $this->urlAddress = $urlAddress;

        return $this;
    }
}
