<?php


namespace Gzhegow\Router\Service\ActionProcessor;


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
     * @param mixed ...$arguments
     *
     * @return null|int|mixed
     */
    public function processAction($action, ...$arguments);
}
