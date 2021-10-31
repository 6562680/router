<?php

namespace Gzhegow\Router\Domain\Handler\Action;

use Gzhegow\Router\Domain\Handler\HandlerInterface;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;
use Gzhegow\Router\Service\ActionProcessor\ActionProcessorInterface;


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
     * @param ActionProcessorInterface     $routeProcessor
     */
    public function __construct($action, ActionProcessorInterface $actionProcessor)
    {
        if (! $actionProcessor->supportsAction($action)) {
            throw new InvalidArgumentException(
                [ 'Invalid action: %s', $action ]
            );
        }

        $this->action = $action;
        $this->actionProcessor = $actionProcessor;
    }


    /**
     * @param mixed ...$arguments
     *
     * @return null|int|mixed
     */
    public function handle(...$arguments)
    {
        $result = $this->actionProcessor->processAction($this->action, ...$arguments);

        return $result;
    }
}
