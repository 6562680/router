<?php

namespace Gzhegow\Router\Domain\Handler\Middleware;

use Gzhegow\Router\Domain\Handler\TapHandler;
use Gzhegow\Router\Domain\Handler\HandlerInterface;
use Gzhegow\Router\Domain\Processor\Middleware\MiddlewareProcessorInterface;


/**
 * GenericMiddleware
 */
class GenericMiddleware implements MiddlewareInterface
{
    /**
     * @var string|object|callable|MiddlewareInterface|mixed
     */
    protected $middleware;
    /**
     * @var MiddlewareProcessorInterface
     */
    protected $middlewareProcessor;

    /**
     * @var HandlerInterface
     */
    protected $next;


    /**
     * Constructor
     *
     * @param string|object|callable|mixed $middleware
     *
     * @param MiddlewareProcessorInterface $middlewareProcessor
     */
    public function __construct($middleware, MiddlewareProcessorInterface $middlewareProcessor)
    {
        $this->middleware = $middleware;
        $this->middlewareProcessor = $middlewareProcessor;
    }


    /**
     * @param mixed $payload
     * @param mixed ...$arguments
     *
     * @return HandlerInterface
     */
    public function handle($payload, ...$arguments)
    {
        $next = $this->next
            ?? new TapHandler();

        $result = $this->middlewareProcessor->processMiddleware($this->middleware, $next, $payload, ...$arguments);

        return $result;
    }


    /**
     * @return HandlerInterface
     */
    public function getNext() : HandlerInterface
    {
        return $this->next;
    }


    /**
     * @param HandlerInterface $next
     *
     * @return static
     */
    public function setNext(HandlerInterface $next)
    {
        $this->next = $next;

        return $this;
    }
}
