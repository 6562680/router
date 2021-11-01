<?php


namespace Gzhegow\Router\Service\ActionProcessor;

use Gzhegow\Router\RouterContainerInterface;


/**
 * CallableActionProcessor
 */
class CallableActionProcessor implements
    ActionProcessorInterface
{
    /**
     * @var RouterContainerInterface
     */
    protected $routerContainer;


    /**
     * Constructor
     *
     * @param RouterContainerInterface $routerContainer
     */
    public function __construct(RouterContainerInterface $routerContainer)
    {
        $this->routerContainer = $routerContainer;
    }


    /**
     * @param callable $action
     * @param mixed    ...$arguments
     *
     * @return null|int|mixed
     */
    public function processAction($action, ...$arguments)
    {
        $route = $this->routerContainer->getRoute();

        $args = func_get_args();
        array_shift($args); // drop action
        $args = $args + $route->getBindings();

        $result = $this->routerContainer->call(null, $action, $args);

        return $result;
    }


    /**
     * > skips \Closure && __invoke()
     * > skips array callables with dynamic object as context
     *
     * @param callable $action
     *
     * @return bool
     */
    public function supportsAction($action) : bool
    {
        return is_callable($action);
    }
}
