<?php


namespace Gzhegow\Router\Service\ActionProcessor;

use Gzhegow\Router\RouterContainerInterface;


/**
 * InvokableClassActionProcessor
 */
class InvokableClassActionProcessor implements
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
     * @param string $action
     * @param mixed  ...$arguments
     *
     * @return null|int|mixed
     */
    public function processAction($action, ...$arguments)
    {
        $route = $this->routerContainer->getRoute();

        $args = func_get_args();
        array_shift($args); // drop action
        $args = $args + $route->getBindings();

        $actionObject = $this->routerContainer->new($action, $args);

        $result = $this->routerContainer->call(null, [ $actionObject, '__invoke' ], $args);

        return $result;
    }


    /**
     * @param string $action
     *
     * @return bool
     */
    public function supportsAction($action) : bool
    {
        if (! class_exists($action)) {
            return false;
        }

        if (! method_exists($action, '__invoke')) {
            return false;
        }

        return true;
    }
}
