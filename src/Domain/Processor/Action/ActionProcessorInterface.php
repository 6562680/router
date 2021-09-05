<?php


namespace Gzhegow\Router\Domain\Processor\Action;


/**
 * ActionProcessorInterface
 */
interface ActionProcessorInterface
{
    /**
     * @param mixed $action
     *
     * @return bool
     */
    public function supportsAction($action) : bool;


    /**
     * @param mixed $action
     * @param mixed $payload
     * @param mixed ...$arguments
     *
     * @return null|int|mixed
     */
    public function processAction($action, $payload, ...$arguments);
}
