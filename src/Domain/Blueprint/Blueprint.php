<?php


namespace Gzhegow\Router\Domain\Blueprint;

use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Exceptions\Runtime\OutOfBoundsException;


/**
 * Blueprint
 */
class Blueprint extends AbstractBlueprint
{
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
        $enum = [
            'CLI' => true,

            'HEAD'    => true,
            'OPTIONS' => true,

            'GET'    => true,
            'POST'   => true,
            'PUT'    => true,
            'PATCH'  => true,
            'DELETE' => true,
            'PURGE'  => true,

            'TRACE'   => true,
            'CONNECT' => true,

            'SOCK' => true,
        ];

        $value = strtoupper(trim($method));

        if (! isset($enum[ $value ])) {
            throw new OutOfBoundsException(
                [ 'Invalid method: %s', $method ]
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
