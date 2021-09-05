<?php


namespace Gzhegow\Router\Domain\Processor\Action;

use Gzhegow\Router\Utils;


/**
 * CallableActionProcessor
 */
class CallableActionProcessor implements ActionProcessorInterface
{
    /**
     * @param mixed $action
     * @param mixed $payload
     * @param mixed ...$arguments
     *
     * @return null|int|mixed
     */
    public function processAction($action, $payload, ...$arguments)
    {
        $result = null;

        // $callable = $action;
        //
        // $route = $this->getRouteCurrent();
        //
        // $result = $this->callAutowired(null, $callable, $route->getBindings());

        return $result;
    }


    /**
     * @param mixed $action
     *
     * @return bool
     */
    public function supportsAction($action) : bool
    {
        // skips \Closure && __invoke()
        // skips array callables with dynamic object as context

        if (is_object($action)) {
            return false;
        }

        return is_callable($action)
            && ! Utils::filterCallableArrayPublic($action);
    }
}
