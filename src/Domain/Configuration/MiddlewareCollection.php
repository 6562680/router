<?php


namespace Gzhegow\Router\Domain\Configuration;

use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;
use Gzhegow\Router\Domain\Handler\Middleware\MiddlewareInterface;
use Gzhegow\Router\Service\ActionProcessor\ActionProcessorInterface;


/**
 * MiddlewareCollection
 */
class MiddlewareCollection
{
    /**
     * @var ActionProcessorInterface
     */
    protected $actionProcessor;

    /**
     * @var array
     */
    protected $middlewareAliases = [];
    /**
     * @var array
     */
    protected $middlewareGroups = [];


    /**
     * Constructor
     *
     * @param ActionProcessorInterface $actionProcessor
     */
    public function __construct(ActionProcessorInterface $actionProcessor)
    {
        $this->actionProcessor = $actionProcessor;
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
     * @return array
     */
    public function getMiddlewareGroup(string $middlewareGroup) : array
    {
        return $this->middlewareGroups[ $middlewareGroup ];
    }


    /**
     * @return string[]
     */
    public function getMiddlewareAliases() : array
    {
        return $this->middlewareAliases;
    }

    /**
     * @param string $middlewareAlias
     *
     * @return string
     */
    public function getMiddlewareAlias(string $middlewareAlias) : string
    {
        return $this->middlewareAliases[ $middlewareAlias ];
    }


    /**
     * @param string $middlewareGroup
     *
     * @return null|array
     */
    public function hasMiddlewareGroup(string $middlewareGroup) : ?array
    {
        return $this->middlewareGroups[ $middlewareGroup ] ?? null;
    }

    /**
     * @param string $middlewareAlias
     *
     * @return null|string
     */
    public function hasMiddlewareAlias(string $middlewareAlias) : ?string
    {
        return $this->middlewareAliases[ $middlewareAlias ] ?? null;
    }


    /**
     * @param MiddlewareInterface|mixed|iterable $middlewareGroups
     *
     * @return static
     */
    public function setMiddlewareGroups($middlewareGroups)
    {
        if (null === $middlewareGroups) {
            $this->middlewareGroups = [];

        } else {
            $this->addMiddlewareGroups($middlewareGroups);
        }

        return $this;
    }

    /**
     * @param MiddlewareInterface|mixed|iterable $middlewareGroups
     *
     * @return static
     */
    public function addMiddlewareGroups($middlewareGroups)
    {
        $middlewareGroups = is_iterable($middlewareGroups)
            ? $middlewareGroups
            : [ $middlewareGroups ];

        foreach ( $middlewareGroups as $middlewareGroupName => $middlewares ) {
            $this->addMiddlewareGroup($middlewareGroupName, $middlewares);
        }

        return $this;
    }

    /**
     * @param MiddlewareInterface|mixed|iterable $middlewares
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
            $middleware = null
                ?? $this->hasMiddlewareAlias($middleware)
                ?? $this->filterMiddleware($middleware);

            if (null === $middleware) {
                throw new InvalidArgumentException(
                    [ 'Unsupported Middleware: %s', $middleware ]
                );
            }
        }

        $this->middlewareGroups[ $middlewareGroupName ] = $middlewares;

        return $this;
    }


    /**
     * @param MiddlewareInterface|mixed|iterable $middlewareAliases
     *
     * @return static
     */
    public function setMiddlewareAliases($middlewareAliases)
    {
        if (null === $middlewareAliases) {
            $this->middlewareAliases = [];

        } else {
            $this->addMiddlewareAliases($middlewareAliases);
        }

        return $this;
    }

    /**
     * @param MiddlewareInterface|mixed|iterable $middlewareAliases
     *
     * @return static
     */
    public function addMiddlewareAliases($middlewareAliases)
    {
        $middlewareAliases = is_iterable($middlewareAliases)
            ? $middlewareAliases
            : [ $middlewareAliases ];

        foreach ( $middlewareAliases as $middlewareName => $middleware ) {
            $this->addMiddlewareAlias($middlewareName, $middleware);
        }

        return $this;
    }

    /**
     * @param string|object|callable|MiddlewareInterface $middleware
     *
     * @return static
     */
    public function addMiddlewareAlias($middlewareAlias, $middleware)
    {
        if (null === $this->filterMiddlewareAlias($middlewareAlias)) {
            throw new InvalidArgumentException(
                [ 'Invalid Middleware Name: %s', $middlewareAlias ]
            );
        }

        if (null === $this->filterMiddleware($middleware)) {
            throw new InvalidArgumentException(
                [ 'Invalid Middleware: %s', $middleware ]
            );
        }

        $this->middlewareAliases[ $middlewareAlias ] = $middleware;

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


    /**
     * @param MiddlewareInterface|mixed $middleware
     */
    public function filterMiddleware($middleware)
    {
        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;

        } elseif ($this->actionProcessor->supportsAction($middleware)) {
            return $middleware;
        }

        return null;
    }


    /**
     * @param string|mixed $middlewareAlias
     *
     * @return null|string
     */
    public function filterMiddlewareAlias($middlewareAlias) : ?string
    {
        if (is_string($middlewareAlias)
            && ( '@' !== $middlewareAlias[ 0 ] ) // we use `@` sign to separate groups from middlewares
            && ( strlen($middlewareAlias) )
        ) {
            return $middlewareAlias;
        }

        return null;
    }
}
