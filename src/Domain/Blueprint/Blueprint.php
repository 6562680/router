<?php


namespace Gzhegow\Router\Domain\Blueprint;

use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;


/**
 * Blueprint
 */
class Blueprint extends AbstractBlueprint
{
    const METHOD_CLI     = 'CLI';
    const METHOD_CONNECT = 'CONNECT';
    const METHOD_DELETE  = 'DELETE';
    const METHOD_GET     = 'GET';
    const METHOD_HEAD    = 'HEAD';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_PATCH   = 'PATCH';
    const METHOD_POST    = 'POST';
    const METHOD_PURGE   = 'PURGE';
    const METHOD_PUT     = 'PUT';
    const METHOD_SOCK    = 'SOCK';
    const METHOD_TRACE   = 'TRACE';

    const THE_METHOD_LIST = [
        self::METHOD_CLI => true,

        self::METHOD_HEAD    => true,
        self::METHOD_OPTIONS => true,

        self::METHOD_GET    => true,
        self::METHOD_POST   => true,
        self::METHOD_PUT    => true,
        self::METHOD_PATCH  => true,
        self::METHOD_DELETE => true,
        self::METHOD_PURGE  => true,

        self::METHOD_TRACE   => true,
        self::METHOD_CONNECT => true,

        self::METHOD_SOCK => true,
    ];


    /**
     * @var string
     */
    protected $method;
    /**
     * @var mixed
     */
    protected $action;


    /**
     * @return Route
     */
    public function build() : Route
    {
        $compiledRoute = new Route(
            $this->getMethod(), $this->getEndpoint(), $this->getAction(),
            $this->getName(), $this->getDescription()
        );

        if (null !== ( $value = $this->getEndpointRegex() )) {
            $compiledRoute->setEndpointRegex($value);
        }

        if ($value = $this->getBindings()) {
            $compiledRoute->setBindings($value);
        }

        if ($value = $this->getMiddlewares()) {
            $compiledRoute->setMiddlewares($value);
        }

        if ($value = $this->getTags()) {
            $compiledRoute->setTags($value);
        }

        if ($value = $this->getCors()) {
            $cors = $value->build();

            $compiledRoute->setCors($cors);
        }

        return $compiledRoute;
    }


    /**
     * @return null|string
     */
    public function getMethod() : ?string
    {
        return $this->method;
    }

    /**
     * @return null|mixed
     */
    public function getAction()
    {
        return $this->action;
    }


    /**
     * @param null|string $method
     *
     * @return static
     */
    public function method(?string $method)
    {
        $value = strtoupper(trim($method));

        if (! isset(static::THE_METHOD_LIST[ $value ])) {
            throw new InvalidArgumentException(
                [ 'Invalid Method: %s', $method ]
            );
        }

        $this->method = $value;

        return $this;
    }

    /**
     * @param null|mixed $action
     *
     * @return static
     */
    public function action($action)
    {
        $this->action = $action;

        return $this;
    }
}
