<?php

namespace Gzhegow\Router\Domain\Blueprint;

use Gzhegow\Router\Domain\Cors\CorsBuilder;


/**
 * AbstractBlueprint
 */
abstract class AbstractBlueprint
{
    /**
     * @var string
     */
    protected $endpoint;
    /**
     * @var string
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
    protected $bindings = [];
    /**
     * @var iterable
     */
    protected $middlewares = [];
    /**
     * @var iterable
     */
    protected $tags = [];

    /**
     * @var CorsBuilder
     */
    protected $cors;


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
    public function getSignature() : ?string
    {
        return $this->signature;
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
     * @return CorsBuilder
     */
    public function getCors() : ?CorsBuilder
    {
        return $this->cors;
    }


    /**
     * @param null|string $endpoint
     *
     * @return static
     */
    public function endpoint(?string $endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * @param null|string $signature
     *
     * @return static
     */
    public function signature(?string $signature)
    {
        $this->signature = $signature;

        return $this;
    }


    /**
     * @param null|string $name
     *
     * @return static
     */
    public function name(?string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param null|string $description
     *
     * @return static
     */
    public function description(?string $description)
    {
        $this->description = $description;

        return $this;
    }


    /**
     * @param null|array $bindings
     *
     * @return static
     */
    public function bindings(?array $bindings)
    {
        if (null === $bindings) {
            $this->bindings = [];

        } else {
            $bindings = $bindings ?? [];

            foreach ( $bindings as $name => $value ) {
                $this->bindings[ $name ] = $value;
            }
        }

        return $this;
    }

    /**
     * @param null|mixed|iterable $middlewares
     *
     * @return static
     */
    public function middlewares($middlewares)
    {
        if (null === $middlewares) {
            $this->middlewares = [];

        } else {
            $middlewares = is_iterable($middlewares)
                ? $middlewares
                : [ $middlewares ];

            foreach ( $middlewares as $middleware ) {
                $key = is_object($middleware)
                    ? get_class($middleware) . '#' . spl_object_id($middleware)
                    : (string) $middleware;

                $this->middlewares[ $key ] = $middleware;
            }
        }

        return $this;
    }

    /**
     * @param null|mixed|iterable $tags
     *
     * @return static
     */
    public function tags($tags)
    {
        if (null === $tags) {
            $this->tags = [];

        } else {
            $tags = is_iterable($tags)
                ? $tags
                : [ $tags ];

            foreach ( $tags as $tag ) {
                $this->tags[ (string) $tag ] = true;
            }
        }

        return $this;
    }


    /**
     * @param null|mixed $cors
     *
     * @return static
     */
    public function cors($cors)
    {
        if (null !== $cors) {
            $cors = CorsBuilder::from($cors);
        }

        $this->cors = $cors;

        return $this;
    }
}
