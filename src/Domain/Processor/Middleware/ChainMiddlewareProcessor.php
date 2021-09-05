<?php


namespace Gzhegow\Router\Domain\Processor\Middleware;

use Gzhegow\Router\Domain\Handler\HandlerInterface;


/**
 * ChainMiddlewareProcessor
 */
class ChainMiddlewareProcessor implements MiddlewareProcessorInterface
{
    /**
     * @var MiddlewareProcessorInterface[]
     */
    protected $middlewareProcessors = [];


    /**
     * Constructor
     *
     * @param MiddlewareProcessorInterface|MiddlewareProcessorInterface[] $middlewareProcessors
     */
    public function __construct($middlewareProcessors)
    {
        $middlewareProcessors = is_array($middlewareProcessors)
            ? $middlewareProcessors
            : [ $middlewareProcessors ];

        foreach ( $middlewareProcessors as $middlewareProcessor ) {
            $this->addMiddlewareProcessor($middlewareProcessor);
        }
    }


    /**
     * @param mixed            $middleware
     * @param HandlerInterface $next
     * @param mixed            $payload
     * @param mixed            ...$arguments
     *
     * @return null|int|mixed
     */
    public function processMiddleware($middleware, HandlerInterface $next, $payload, ...$arguments)
    {
        $result = null;

        foreach ( $this->middlewareProcessors as $middlewareProcessor ) {
            if ($middlewareProcessor->supportsMiddleware($middleware)) {
                $result = $middlewareProcessor->processMiddleware($middleware, $next, $payload, ...$arguments);

                break;
            }
        }

        return $result;
    }


    /**
     * @param MiddlewareProcessorInterface $middlewareProcessor
     *
     * @return static
     */
    protected function addMiddlewareProcessor(MiddlewareProcessorInterface $middlewareProcessor)
    {
        $this->middlewareProcessors[] = $middlewareProcessor;

        return $this;
    }


    /**
     * @param mixed $middleware
     *
     * @return bool
     */
    public function supportsMiddleware($middleware) : bool
    {
        foreach ( $this->middlewareProcessors as $child ) {
            if ($child->supportsMiddleware($middleware)) {
                return true;
            }
        }

        return false;
    }
}
