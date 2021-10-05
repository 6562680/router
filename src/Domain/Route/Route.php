<?php


namespace Gzhegow\Router\Domain\Route;

use Gzhegow\Router\Domain\Cors\Cors;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;


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
     * @var mixed
     */
    protected $action;

    /**
     * @var string
     */
    protected $endpoint;
    /**
     * @var string
     */
    protected $endpointRegex;

    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $description;

    /**
     * @var array
     */
    protected $bindings = [];
    /**
     * @var array
     */
    protected $middlewares = [];
    /**
     * @var array
     */
    protected $tags = [];

    /**
     * @var Cors
     */
    protected $cors;


    /**
     * Constructor
     *
     * @param string      $method
     * @param string      $endpoint
     * @param             $action
     *
     * @param null|string $name
     * @param null|string $description
     */
    public function __construct(string $method, string $endpoint, $action,
        string $name = null,
        string $description = null
    )
    {
        $this->method = $method;
        $this->action = $action;

        $this->endpoint = $endpoint;

        $this->name = $name;
        $this->description = $description;
    }


    /**
     * @return array
     */
    public function __serialize() : array
    {
        return array_filter([
            'method' => $this->method,
            'action' => $this->action,

            'endpoint'      => $this->endpoint,
            'endpointRegex' => $this->endpointRegex,

            'name'        => $this->name,
            'description' => $this->description,

            'bindings'    => $this->bindings,
            'middlewares' => $this->middlewares,
            'tags'        => $this->tags,

            'cors' => $this->cors,
        ], function ($v) { return ! is_null($v); });
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function __unserialize(array $data) : void
    {
        $this->method = $data[ 'method' ] ?? null;
        $this->action = $data[ 'action' ] ?? null;

        $this->endpoint = $data[ 'endpoint' ] ?? null;
        $this->endpointRegex = $data[ 'endpointRegex' ] ?? null;

        $this->name = $data[ 'name' ] ?? null;
        $this->description = $data[ 'description' ] ?? null;

        $this->bindings = $data[ 'bindings' ] ?? [];
        $this->middlewares = $data[ 'middlewares' ] ?? [];
        $this->tags = $data[ 'tags' ] ?? [];

        $this->cors = $data[ 'cors' ] ?? null;
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
     * @return null|string
     */
    public function getEndpoint() : ?string
    {
        return $this->endpoint;
    }

    /**
     * @return null|string
     */
    public function getEndpointRegex() : ?string
    {
        return $this->endpointRegex;
    }


    /**
     * @return null|string
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }


    /**
     * @return array
     */
    public function getBindings() : array
    {
        return $this->bindings;
    }

    /**
     * @return null|mixed
     */
    public function getBinding(string $id)
    {
        return $this->bindings[ $id ] ?? null;
    }


    /**
     * @return array
     */
    public function getMiddlewares() : array
    {
        return $this->middlewares;
    }

    /**
     * @return array
     */
    public function getTags() : array
    {
        return array_keys($this->tags);
    }


    /**
     * @return Cors
     */
    public function getCors() : Cors
    {
        return $this->cors;
    }


    /**
     * @param null|string $endpointRegex
     *
     * @return static
     */
    public function setEndpointRegex(?string $endpointRegex)
    {
        if (null !== $endpointRegex) {
            if (false === @preg_match($endpointRegex, '')) {
                throw new InvalidArgumentException(
                    [ 'Bad regular expression: %s', $endpointRegex ]
                );
            }
        }

        $this->endpointRegex = $endpointRegex;

        return $this;
    }


    /**
     * @param null|array $bindings
     *
     * @return static
     */
    public function setBindings(?array $bindings)
    {
        if (null === $bindings) {
            $this->bindings = [];

        } else {
            $this->addBindings($bindings);
        }

        return $this;
    }

    /**
     * @param array $bindings
     *
     * @return static
     */
    public function addBindings(array $bindings)
    {
        foreach ( $bindings as $binding => $bindingValue ) {
            $this->setBinding($binding, $bindingValue);
        }

        return $this;
    }

    /**
     * @param string     $binding
     * @param null|mixed $value
     *
     * @return static
     */
    public function setBinding(string $binding, $value = null)
    {
        $this->bindings[ $binding ] = $value;

        return $this;
    }


    /**
     * @param null|mixed|iterable $middlewares
     *
     * @return static
     */
    public function setMiddlewares($middlewares)
    {
        $this->middlewares = [];

        if (null !== $middlewares) {
            $this->addMiddlewares($middlewares);
        }

        return $this;
    }

    /**
     * @param mixed|iterable $middlewares
     *
     * @return static
     */
    public function addMiddlewares($middlewares)
    {
        $middlewares = is_iterable($middlewares)
            ? $middlewares
            : [ $middlewares ];

        foreach ( $middlewares as $middleware ) {
            $this->addMiddleware($middleware);
        }

        return $this;
    }

    /**
     * @param mixed $middleware
     */
    public function addMiddleware($middleware)
    {
        $key = is_object($middleware)
            ? get_class($middleware) . '#' . spl_object_id($middleware)
            : (string) $middleware;

        $this->middlewares[ $key ] = $middleware;

        return $this;
    }


    /**
     * @param null|string|string[] $tags
     *
     * @return static
     */
    public function setTags($tags)
    {
        $this->tags = [];

        if (null !== $tags) {
            $this->addTags($tags);
        }

        return $this;
    }

    /**
     * @param string|string[] $tags
     *
     * @return static
     */
    public function addTags($tags)
    {
        $tags = is_iterable($tags)
            ? $tags
            : [ $tags ];

        foreach ( $tags as $tag ) {
            $this->addTag($tag);
        }

        return $this;
    }

    /**
     * @param string $tag
     *
     * @return static
     */
    public function addTag(string $tag)
    {
        $this->tags[ $tag ] = true;

        return $this;
    }


    /**
     * @param Cors $cors
     *
     * @return static
     */
    public function setCors(Cors $cors)
    {
        $this->cors = $cors;

        return $this;
    }
}
