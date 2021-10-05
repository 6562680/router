<?php

namespace Gzhegow\Router\Domain\Handler\Middleware;

use Gzhegow\Router\Domain\Handler\TapHandler;
use Gzhegow\Router\Domain\Handler\HandlerInterface;
use Gzhegow\Router\Service\ActionProcessor\ActionProcessorInterface;


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
     * @var ActionProcessorInterface
     */
    protected $actionProcessor;

    /**
     * @var HandlerInterface
     */
    protected $next;


    /**
     * Constructor
     *
     * @param string|object|callable|MiddlewareInterface|mixed $middleware
     *
     * @param ActionProcessorInterface                         $actionProcessor
     */
    public function __construct($middleware, ActionProcessorInterface $actionProcessor)
    {
        $this->middleware = $middleware;

        $this->actionProcessor = $actionProcessor;
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

        $result = $this->actionProcessor->processAction($this->middleware, $next, $payload, ...$arguments);

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
