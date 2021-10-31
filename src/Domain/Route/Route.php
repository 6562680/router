<?php


namespace Gzhegow\Router\Domain\Route;

use Gzhegow\Router\Domain\Endpoint\Endpoint;
use Gzhegow\Router\Domain\Endpoint\Signature;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;
use Gzhegow\Router\Exceptions\Runtime\UnexpectedValueException;


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
     * @var Endpoint
     */
    protected $endpoint;
    /**
     * @var Signature
     */
    protected $signature;

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
    protected $bindings;
    /**
     * @var array
     */
    protected $middlewares;
    /**
     * @var array
     */
    protected $tags;


    /**
     * Constructor
     *
     * @param string $method
     * @param string $command
     * @param mixed  $action
     */
    public function __construct(string $method, string $command, $action)
    {
        if (! strlen($method)) {
            throw new InvalidArgumentException(
                [ 'Invalid method: %s', $method ]
            );
        }

        [ $endpoint, $signature ] = explode(' ', $command, 2) + [ null, null ];

        $this->method = $method;
        $this->action = $action;

        $this->endpoint = new Endpoint($endpoint);

        if (null !== $signature) {
            $this->signature = new Signature($signature);
        }
    }


    /**
     * @return array
     */
    public function __serialize() : array
    {
        return array_filter([
            'method'   => $this->method,
            'endpoint' => $this->endpoint,
            'action'   => $this->action,

            'name'        => $this->name,
            'description' => $this->description,

            'bindings'    => $this->bindings,
            'middlewares' => $this->middlewares,
            'tags'        => $this->tags,
        ], function ($v) {
            return ! is_null($v);
        });
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function __unserialize(array $data) : void
    {
        $this->method = $data[ 'method' ] ?? null;
        $this->endpoint = $data[ 'endpoint' ] ?? null;
        $this->action = $data[ 'action' ] ?? null;

        $this->name = $data[ 'name' ] ?? null;
        $this->description = $data[ 'description' ] ?? null;

        $this->bindings = $data[ 'bindings' ] ?? [];
        $this->middlewares = $data[ 'middlewares' ] ?? [];
        $this->tags = $data[ 'tags' ] ?? [];
    }


    /**
     * @return null|string
     */
    public function getMethod() : string
    {
        return $this->method;
    }

    /**
     * @return null|mixed
     */
    public function getAction()
    {
        if (null === $this->action) {
            throw new UnexpectedValueException(
                [ 'Missing action: %s', $this ]
            );
        }

        return $this->action;
    }


    /**
     * @return Endpoint
     */
    public function getEndpoint() : Endpoint
    {
        return $this->endpoint;
    }

    /**
     * @return Signature
     */
    public function getSignature() : Signature
    {
        return $this->signature;
    }


    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }


    /**
     * @return array
     */
    public function getBindings() : array
    {
        return $this->bindings ?? [];
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
        return $this->middlewares ?? [];
    }

    /**
     * @return array
     */
    public function getTags() : array
    {
        return array_keys($this->tags ?? []);
    }


    /**
     * @return Signature
     */
    public function hasSignature() : ?Signature
    {
        return $this->signature;
    }


    /**
     * @return null|string
     */
    public function hasName() : ?string
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function hasDescription() : ?string
    {
        return $this->description;
    }


    /**
     * @param null|string $name
     *
     * @return static
     */
    public function setName(?string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param null|string $description
     *
     * @return static
     */
    public function setDescription(?string $description)
    {
        $this->description = $description;

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
            $this->bindings = null;

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
        if (null === $middlewares) {
            $this->middlewares = null;

        } else {
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
        if (null === $tags) {
            $this->tags = null;

        } else {
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
}
