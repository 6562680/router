<?php

namespace Gzhegow\Router\Domain\Route;

use Gzhegow\Router\Utils;
use Gzhegow\Router\Domain\Configuration\Configuration;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;
use Gzhegow\Router\Domain\Handler\Middleware\MiddlewareInterface;


/**
 * BlueprintNode
 */
class BlueprintNode
{
    /**
     * @var Configuration
     */
    protected $configuration;


    /**
     * @var null|string
     */
    protected $endpoint;

    /**
     * @var null|string
     */
    protected $namespace;

    /**
     * @var null|string
     */
    protected $name;

    /**
     * @var array
     */
    protected $bindings = [];
    /**
     * @var array
     */
    protected $tags = [];

    /**
     * @var string|callable|object|MiddlewareInterface|array
     */
    protected $middlewares = [];


    /**
     * Constructor
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }


    /**
     * @return Configuration
     */
    public function getConfiguration() : Configuration
    {
        return $this->configuration;
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
    public function getNamespace() : ?string
    {
        return $this->namespace;
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
     * @return array
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
     * @param null|string $endpoint
     *
     * @return static
     */
    public function setEndpoint(?string $endpoint)
    {
        if (null !== $endpoint) {
            if ('' === $endpoint) {
                throw new InvalidArgumentException(
                    [ 'Invalid endpoint: %s', $endpoint ]
                );
            }

            $separators = implode('', $this->configuration->getSeparatorCollection()->getSeparators());

            $endpoint = trim($endpoint);
            $endpoint = trim($endpoint, $separators);
        }

        $this->endpoint = $endpoint;

        return $this;
    }


    /**
     * @param null|string $namespace
     *
     * @return static
     */
    public function setNamespace(?string $namespace)
    {
        if (null !== $namespace) {
            if (null === $this->filterNamespace($namespace)) {
                throw new InvalidArgumentException(
                    [ 'Invalid namespace: %s', $namespace ]
                );
            }

            $namespace = trim($namespace, '\\');
        }

        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @param null|string $name
     *
     * @return static
     */
    public function setName(?string $name)
    {
        if (null !== $name) {
            if (null === $this->filterName($name)) {
                throw new InvalidArgumentException(
                    [ 'Invalid name: %s', $name ]
                );
            }

            $name = trim($name, '.');
        }

        $this->name = $name;

        return $this;
    }


    /**
     * @param array $bindings
     *
     * @return static
     */
    public function setBindings(array $bindings)
    {
        $this->bindings = [];

        foreach ( $bindings as $attr => $value ) {
            $this->setBinding($attr, $value);
        }

        return $this;
    }

    /**
     * @param string      $binding
     * @param null|string $value
     *
     * @return static
     */
    public function setBinding(string $binding = '', string $value = null)
    {
        if ('' === $binding) {
            throw new InvalidArgumentException(
                [ 'Invalid binding: %s', $binding ]
            );
        }

        $this->bindings[ $binding ] = $value;

        return $this;
    }


    /**
     * @param array $tags
     *
     * @return static
     */
    public function setTags(array $tags)
    {
        $this->tags = [];

        $this->addTags($tags);

        return $this;
    }

    /**
     * @param array $tags
     *
     * @return static
     */
    public function addTags(array $tags)
    {
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
    public function addTag(string $tag = '')
    {
        if (null === $this->filterTag($tag)) {
            throw new InvalidArgumentException(
                [ 'Invalid tag: %s', $tag ]
            );
        }

        $this->tags[ $tag ] = true;

        return $this;
    }


    /**
     * @param string|string[] $middlewares
     *
     * @return static
     */
    public function setMiddlewares($middlewares)
    {
        $middlewares = is_array($middlewares)
            ? $middlewares
            : [ $middlewares ];

        $this->middlewares = [];

        $this->addMiddlewares($middlewares);

        return $this;
    }

    /**
     * @param string|string[] $middlewares
     *
     * @return static
     */
    public function addMiddlewares($middlewares)
    {
        $middlewares = is_array($middlewares)
            ? $middlewares
            : [ $middlewares ];

        foreach ( $middlewares as $middleware ) {
            $this->addMiddleware($middleware);
        }

        return $this;
    }

    /**
     * @param string|callable|object|mixed $middleware
     *
     * @return static
     */
    public function addMiddleware($middleware)
    {
        if (null === $this->configuration->filterMiddleware($middleware)) {
            throw new InvalidArgumentException(
                [ 'Invalid middleware: %s', $middleware ]
            );
        }

        $uniq = null
            ?? ( is_object($middleware) ? get_class($middleware) : null )
            ?? crc32(serialize($middleware));

        $this->middlewares[ $uniq ] = $middleware;

        return $this;
    }


    /**
     * @param string|mixed $namespace
     *
     * @return null|string
     */
    public function filterNamespace($namespace) : ?string
    {
        if (null === Utils::filterClassFullname($namespace)) {
            return null;
        }

        return $namespace;
    }

    /**
     * @param string|mixed $name
     *
     * @return null|string
     */
    public function filterName($name) : ?string
    {
        if ('' === $name) {
            return null;
        }

        return $name;
    }


    /**
     * @param string|mixed $tag
     *
     * @return null|string
     */
    public function filterTag($tag) : ?string
    {
        if ('' === $tag) {
            return null;
        }

        return $tag;
    }


    /**
     * @param string $endpoint
     *
     * @return static
     */
    public function endpoint(string $endpoint)
    {
        $this->setEndpoint($endpoint);

        return $this;
    }


    /**
     * @param null|string $namespace
     *
     * @return static
     */
    public function namespace(?string $namespace)
    {
        $this->setNamespace($namespace);

        return $this;
    }

    /**
     * @param null|string $name
     *
     * @return static
     */
    public function name(?string $name)
    {
        $this->setName($name);

        return $this;
    }


    /**
     * @param array $bindings
     *
     * @return static
     */
    public function bindings(array $bindings)
    {
        $this->setBindings($bindings);

        return $this;
    }

    /**
     * @param array $tags
     *
     * @return static
     */
    public function tags($tags)
    {
        $this->addTags($tags);

        return $this;
    }


    /**
     * @param string|string[] $middlewares
     *
     * @return static
     */
    public function middlewares($middlewares)
    {
        $this->addMiddlewares($middlewares);

        return $this;
    }
}
