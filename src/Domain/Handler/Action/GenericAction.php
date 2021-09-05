<?php

namespace Gzhegow\Router\Domain\Handler\Action;

use Gzhegow\Router\Domain\Handler\HandlerInterface;
use Gzhegow\Router\Domain\Processor\Action\ActionProcessorInterface;


/**
 * GenericAction
 */
class GenericAction implements HandlerInterface
{
    /**
     * @var string|callable|HandlerInterface|mixed
     */
    protected $action;
    /**
     * @var ActionProcessorInterface
     */
    protected $actionProcessor;


    /**
     * Constructor
     *
     * @param string|object|callable|mixed $action
     */
    public function __construct($action, ActionProcessorInterface $routeProcessor)
    {
        $this->action = $action;
        $this->actionProcessor = $routeProcessor;
    }


    /**
     * @param mixed $payload
     * @param mixed ...$arguments
     *
     * @return null|int|mixed
     */
    public function handle($payload, ...$arguments)
    {
        $result = $this->actionProcessor->processAction($this->action, $payload, ...$arguments);

        return $result;
    }
}
