<?php


namespace Gzhegow\Router\Service\ActionProcessor\Logic;

use Gzhegow\Router\Service\ActionProcessor\ActionProcessorInterface;


/**
 * PipeActionProcessor
 */
class PipeActionProcessor implements ActionProcessorInterface
{
    /**
     * @var ActionProcessorInterface[]
     */
    protected $actionProcessors = [];


    /**
     * Constructor
     *
     * @param ActionProcessorInterface|ActionProcessorInterface[] $actionProcessors
     */
    public function __construct($actionProcessors = [])
    {
        $actionProcessors = is_iterable($actionProcessors)
            ? $actionProcessors
            : [ $actionProcessors ];

        foreach ( $actionProcessors as $actionProcessor ) {
            $this->addActionProcessor($actionProcessor);
        }
    }


    /**
     * @param mixed $action
     * @param mixed ...$arguments
     *
     * @return null|int|mixed
     */
    public function processAction($action, ...$arguments)
    {
        $result = null !== key($arguments)
            ? array_shift($arguments)
            : null;

        foreach ( $this->actionProcessors as $child ) {
            if ($child->supportsAction($action)) {
                $result = $child->processAction($action, $result, ...$arguments);
            }
        }

        return $result;
    }


    /**
     * @param ActionProcessorInterface $actionProcessor
     *
     * @return static
     */
    public function addActionProcessor(ActionProcessorInterface $actionProcessor)
    {
        $this->actionProcessors[] = $actionProcessor;

        return $this;
    }


    /**
     * @param mixed $action
     *
     * @return bool
     */
    public function supportsAction($action) : bool
    {
        foreach ( $this->actionProcessors as $child ) {
            if ($child->supportsAction($action)) {
                return true;
            }
        }

        return false;
    }
}
