<?php


namespace Gzhegow\Router\Service\ActionProcessor;

use Gzhegow\Router\RouterContainerInterface;
use Gzhegow\Router\Domain\Handler\HandlerInterface;


/**
 * HandlerActionProcessor
 */
class HandlerActionProcessor implements
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
     * @param HandlerInterface $action
     * @param mixed            ...$arguments
     *
     * @return null|int|mixed|void
     */
    public function processAction($action, ...$arguments)
    {
        $route = $this->routerContainer->getRoute();

        $args = func_get_args();
        array_shift($args); // drop action
        $args = $args + $route->getBindings();

        $actionObject = is_object($action)
            ? $action
            : $this->routerContainer->new($action, $args);

        $result = $this->routerContainer->call(null, [ $actionObject, 'handle' ], $args);

        return $result;
    }


    /**
     * @param HandlerInterface $action
     *
     * @return bool
     */
    public function supportsAction($action) : bool
    {
        return is_a($action, HandlerInterface::class, true);
    }
}
