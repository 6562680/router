<?php


namespace Gzhegow\Router\Domain\Configuration;

use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;
use Gzhegow\Router\Domain\Handler\Middleware\MiddlewareInterface;
use Gzhegow\Router\Domain\Processor\Middleware\MiddlewareProcessorInterface;


/**
 * MiddlewareGroupCollection
 */
class MiddlewareGroupCollection
{
    /**
     * @var MiddlewareProcessorInterface
     */
    protected $middlewareProcessor;

    /**
     * @var array
     */
    protected $middlewareGroups = [];


    /**
     * Constructor
     *
     * @param MiddlewareProcessorInterface $middlewareProcessor
     */
    public function __construct(MiddlewareProcessorInterface $middlewareProcessor)
    {
        $this->middlewareProcessor = $middlewareProcessor;
    }


    /**
     * @return string[]
     */
    public function getMiddlewareGroups() : array
    {
        return $this->middlewareGroups;
    }

    /**
     * @param string $middlewareGroup
     *
     * @return string
     */
    public function getMiddlewareGroup(string $middlewareGroup) : string
    {
        return $this->middlewareGroups[ $middlewareGroup ];
    }


    /**
     * @param string $middlewareGroup
     *
     * @return null|string
     */
    public function hasMiddlewareGroup(string $middlewareGroup) : ?string
    {
        return $this->middlewareGroups[ $middlewareGroup ] ?? null;
    }


    /**
     * @param string|object|callable|MiddlewareInterface|array $middlewareGroups
     *
     * @return static
     */
    public function setMiddlewareGroups($middlewareGroups)
    {
        $middlewareGroups = is_array($middlewareGroups)
            ? $middlewareGroups
            : [ $middlewareGroups ];

        $this->middlewareGroups = [];

        $this->addMiddlewareGroups($middlewareGroups);

        return $this;
    }

    /**
     * @param string|object|callable|MiddlewareInterface|array $middlewareGroups
     *
     * @return static
     */
    public function addMiddlewareGroups($middlewareGroups)
    {
        $middlewareGroups = is_array($middlewareGroups)
            ? $middlewareGroups
            : [ $middlewareGroups ];

        foreach ( $middlewareGroups as $middlewareGroupName => $middlewares ) {
            $this->addMiddlewareGroup($middlewareGroupName, $middlewares);
        }

        return $this;
    }

    /**
     * @param string|object|callable|MiddlewareInterface $middlewares
     *
     * @return static
     */
    public function addMiddlewareGroup($middlewareGroupName, $middlewares)
    {
        if (null === $this->filterMiddlewareGroupName($middlewareGroupName)) {
            throw new InvalidArgumentException(
                [ 'Invalid MiddlewareGroupName: %s', $middlewareGroupName ]
            );
        }

        $middlewares = is_iterable($middlewares)
            ? $middlewares
            : [ $middlewares ];

        foreach ( $middlewares as $middleware ) {
            if (! $this->middlewareProcessor->supportsMiddleware($middleware)) {
                throw new InvalidArgumentException(
                    [ 'Unsupported Middleware: %s', $middleware ]
                );
            }
        }

        $this->middlewareGroups[ $middlewareGroupName ] = $middlewares;

        return $this;
    }


    /**
     * @param string|mixed $middlewareGroupName
     *
     * @return null|string
     */
    public function filterMiddlewareGroupName($middlewareGroupName) : ?string
    {
        if (is_string($middlewareGroupName)
            && ( '@' === $middlewareGroupName[ 0 ] ) // we use `@` sign to separate groups from middlewares
            && ( strlen($middlewareGroupName) > 1 )
        ) {
            return $middlewareGroupName;
        }

        return null;
    }
}
