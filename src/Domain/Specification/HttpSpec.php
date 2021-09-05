<?php


namespace Gzhegow\Router\Domain\Specification;

use Gzhegow\Router\Domain\Configuration\Configuration;
use Gzhegow\Router\Domain\Route\Route;


/**
 * HttpSpec
 */
class HttpSpec implements SpecificationInterface
{
    /**
     * @var Configuration
     */
    protected $configuration;

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
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        $this->httpMethod = strtoupper($_SERVER[ 'REQUEST_METHOD' ] ?? '') ?: null;
    }


    /**
     * @return Configuration
     */
    public function getConfiguration() : Configuration
    {
        return $this->configuration;
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
     * @param Route $route
     *
     * @return bool
     */
    public function isMatch(Route $route) : bool
    {
        if (! ( $route->isCompiled()
            && ( $this->httpMethod === $route->getMethod() )
            && ( preg_match($route->getEndpointCompiled(), $this->urlAddress, $matches) )
        )) {
            return false;
        }

        $bindings = [];
        foreach ( $this->configuration->getEndpointPatterns() as $pattern => $regex ) {
            if (isset($matches[ $pattern ])) {
                // append '$' sign to allow autowiring
                $bindings[ '$' . $pattern ] = $matches[ $pattern ];
            }
        }

        $bindings += $route->getBindings();

        $route->withBindings($bindings);

        return true;
    }


    /**
     * @param string $method
     *
     * @return static
     */
    public function httpMethod(string $method)
    {
        $this->httpMethod = strtoupper($method);

        return $this;
    }

    /**
     * @param string $urlAddress
     *
     * @return static
     */
    public function urlAddress(string $urlAddress)
    {
        $trim = $urlAddress;
        $trim = trim($trim);
        $trim = trim($trim, implode('', $this->configuration->getEndpointSeparators()));

        $this->urlAddress = $trim;

        return $this;
    }
}
