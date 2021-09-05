<?php


namespace Gzhegow\Router\Domain\Configuration;

use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;
use Gzhegow\Router\Domain\Handler\Middleware\MiddlewareInterface;
use Gzhegow\Router\Domain\Processor\Middleware\MiddlewareProcessorInterface;


/**
 * MiddlewareCollection
 */
class MiddlewareCollection
{
    /**
     * @var MiddlewareProcessorInterface
     */
    protected $middlewareProcessor;

    /**
     * @var array
     */
    protected $middlewares = [];


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
    public function getMiddlewares() : array
    {
        return $this->middlewares;
    }

    /**
     * @param string $middleware
     *
     * @return string
     */
    public function getMiddleware(string $middleware) : string
    {
        return $this->middlewares[ $middleware ];
    }


    /**
     * @param string $middleware
     *
     * @return null|string
     */
    public function hasMiddleware(string $middleware) : ?string
    {
        return $this->middlewares[ $middleware ] ?? null;
    }


    /**
     * @param string|object|callable|MiddlewareInterface|array $middlewares
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
     * @param string|object|callable|MiddlewareInterface|array $middlewares
     *
     * @return static
     */
    public function addMiddlewares($middlewares)
    {
        $middlewares = is_array($middlewares)
            ? $middlewares
            : [ $middlewares ];

        foreach ( $middlewares as $middlewareName => $middleware ) {
            $this->addMiddleware($middlewareName, $middleware);
        }

        return $this;
    }

    /**
     * @param string|object|callable|MiddlewareInterface $middleware
     *
     * @return static
     */
    public function addMiddleware($middlewareName, $middleware)
    {
        if (null === $this->filterMiddlewareName($middlewareName)) {
            throw new InvalidArgumentException(
                [ 'Invalid MiddlewareName: %s', $middlewareName ]
            );
        }

        if (! $this->middlewareProcessor->supportsMiddleware($middleware)) {
            throw new InvalidArgumentException(
                [ 'Invalid Middleware: %s', $middleware ]
            );
        }

        $this->middlewares[ $middlewareName ] = $middleware;

        return $this;
    }


    /**
     * @param string|mixed $middlewareName
     *
     * @return null|string
     */
    public function filterMiddlewareName($middlewareName) : ?string
    {
        if (is_string($middlewareName)
            && ( '@' !== $middlewareName[ 0 ] ) // we use `@` sign to separate groups from middlewares
            && ( strlen($middlewareName) )
        ) {
            return $middlewareName;
        }

        return null;
    }
}
