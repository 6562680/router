<?php


namespace Gzhegow\Router\Service\ActionProcessor;

use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\RouterContainerInterface;
use Gzhegow\Router\Domain\Handler\HandlerInterface;
use Gzhegow\Router\Domain\Route\RouteAwareInterface;


/**
 * HandlerActionProcessor
 */
class HandlerActionProcessor implements
    ActionProcessorInterface,
    RouteAwareInterface
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
        $route = $this->getRoute();

        $object = is_object($action)
            ? $action
            : $this->routerContainer->new($action, $route->getBindings());

        $callable = [ $object, 'handle' ];

        $args = func_get_args();
        array_shift($args);
        $args = $args + $route->getBindings();

        $result = $this->routerContainer->call(null, $callable, $args);

        return $result;
    }


    /**
     * @return Route
     */
    public function getRoute() : Route
    {
        return $this->routerContainer->get(Route::class);
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
