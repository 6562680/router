<?php


namespace Gzhegow\Router\Domain\Route;

use Gzhegow\Router\Domain\Handler\HandlerInterface;
use Gzhegow\Router\Exceptions\Runtime\OutOfBoundsException;
use Gzhegow\Router\Domain\Handler\Middleware\MiddlewareInterface;


/**
 * Route
 */
class Route
{
    /**
     * @var string
     */
    protected $method;
    /**
     * @var string
     */
    protected $endpoint;
    /**
     * @var string|callable|HandlerInterface|mixed
     */
    protected $action;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $bindings = [];
    /**
     * @var string[]
     */
    protected $tags = [];

    /**
     * @var string|object|callable|MiddlewareInterface|array
     */
    protected $middlewares = [];


    /**
     * @var string
     */
    protected $endpointCompiled;


    /**
     * Constructor
     *
     * @param string      $method
     * @param string      $endpoint
     * @param callable    $action
     *
     * @param null|string $name
     *
     * @param null|array  $bindings
     * @param null|array  $tags
     *
     * @param null|array  $middlewares
     */
    public function __construct(
        string $method,
        string $endpoint,
        callable $action,

        string $name = null,

        array $bindings = null,
        array $tags = null,

        array $middlewares = null
    )
    {
        $this->method = $method;
        $this->endpoint = $endpoint;
        $this->action = $action;

        $this->name = $name;

        $this->bindings = $bindings ?? [];
        $this->tags = $tags ?? [];

        $this->middlewares = $middlewares ?? [];
    }


    /**
     * @param string $endpointCompiled
     *
     * @return static
     */
    public function withEndpointCompiled(string $endpointCompiled)
    {
        $this->endpointCompiled = $endpointCompiled;

        return $this;
    }


    /**
     * @param array $bindings
     *
     * @return static
     */
    public function withBindings(array $bindings)
    {
        foreach ( $bindings as $binding => $bindingValue ) {
            $this->withBinding($binding, $bindingValue);
        }

        return $this;
    }

    /**
     * @param string     $binding
     * @param null|mixed $value
     *
     * @return static
     */
    public function withBinding(string $binding, $value = null)
    {
        if (! isset($this->bindings[ $binding ])) {
            throw new OutOfBoundsException(
                [ 'Unknown binding: %s', $binding ]
            );
        }

        $this->bindings[ $binding ] = $value;

        return $this;
    }


    /**
     * @return string
     */
    public function getMethod() : string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getEndpoint() : string
    {
        return $this->endpoint;
    }

    /**
     * @return string|callable|HandlerInterface|mixed
     */
    public function getAction()
    {
        return $this->action;
    }


    /**
     * @return null|string
     */
    public function getName() : ?string
    {
        return $this->name;
    }


    /**
     * @return array
     */
    public function getBindings() : array
    {
        return $this->bindings;
    }

    /**
     * @return mixed
     */
    public function getBinding(string $id)
    {
        return $this->bindings[ $id ];
    }


    /**
     * @return string[]
     */
    public function getTags() : array
    {
        return $this->tags;
    }


    /**
     * @return array
     */
    public function getMiddlewares() : array
    {
        return $this->middlewares;
    }


    /**
     * @return string
     */
    public function getEndpointCompiled() : string
    {
        return $this->endpointCompiled;
    }


    /**
     * @param string $id
     *
     * @return null|mixed
     */
    public function hasBinding(string $id)
    {
        return $this->bindings[ $id ] ?? null;
    }


    /**
     * @return bool
     */
    public function isCompiled() : bool
    {
        return isset($this->endpointCompiled);
    }
}
