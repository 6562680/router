<?php


namespace Gzhegow\Router\Domain\Route\Annotation;

/**
 * Route
 *
 * @Annotation
 * @Target("METHOD")
 */
class Route
{
    /**
     * @Required
     *
     * @var string
     */
    public $endpoint;

    /**
     * @var string
     */
    public $method;


    /**
     * Constructor
     *
     * @param string      $endpoint
     * @param null|string $method
     */
    public function __construct(string $endpoint, string $method = null)
    {
        $this->endpoint = $endpoint;
        $this->method = $method ?? 'GET';
    }


    /**
     * @return string
     */
    public function getEndpoint() : string
    {
        return $this->endpoint;
    }

    /**
     * @return string
     */
    public function getMethod() : string
    {
        return $this->method;
    }
}
