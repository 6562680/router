<?php


namespace Gzhegow\Router\Domain\Route\Specification;

use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Exceptions\Runtime\BadMethodCallException;


/**
 * HttpRouteSpecification
 */
class HttpRouteSpecification implements RouteSpectificationInterface
{
    /**
     * @var string
     */
    protected $urlAddress;

    /**
     * @var null|string
     */
    protected $httpMethod;


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
    public function getUrlAddress() : string
    {
        return $this->urlAddress;
    }


    /**
     * @return null|string
     */
    public function getHttpMethod() : ?string
    {
        return $this->httpMethod;
    }


    /**
     * @param Route $route
     *
     * @return bool
     */
    public function isMatch(Route $route) : bool
    {
        if (null === $this->urlAddress) {
            throw new BadMethodCallException(
                [ 'This specification requires URL address' ],
            );
        }

        $isMethodEmpty = null === $this->httpMethod;
        $isMethodMatch = $this->httpMethod === $route->getMethod();
        if (! ( $isMethodEmpty || $isMethodMatch )) {
            return false;
        }

        if (! preg_match($route->getEndpoint(), $this->urlAddress, $matches)) {
            return false;
        }

        $bindings = $route->getBindings();

        foreach ( $matches as $idx => $value ) {
            if (is_string($idx)) {
                $bindings[ $idx ] = $value;
            }
        }

        $route->setBindings($bindings);

        return true;
    }


    /**
     * @param string $urlAddress
     *
     * @return static
     */
    public function urlAddress(string $urlAddress)
    {
        $urlAddress = ltrim(trim($urlAddress), '/');

        $this->urlAddress = $urlAddress;

        return $this;
    }


    /**
     * @param null|string $httpMethod
     *
     * @return static
     */
    public function httpMethod(?string $httpMethod)
    {
        if (null !== $httpMethod) {
            $httpMethod = strtoupper(trim($httpMethod));
        }

        $this->httpMethod = $httpMethod;

        return $this;
    }
}
