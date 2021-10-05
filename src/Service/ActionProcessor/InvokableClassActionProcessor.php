<?php


namespace Gzhegow\Router\Service\ActionProcessor;

use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\RouterContainerInterface;
use Gzhegow\Router\Domain\Route\RouteAwareInterface;


/**
 * InvokableClassActionProcessor
 */
class InvokableClassActionProcessor implements
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
     * @param string $action
     * @param mixed  $payload
     * @param mixed  ...$arguments
     *
     * @return null|int|mixed
     */
    public function processAction($action, $payload, ...$arguments)
    {
        $route = $this->getRoute();

        $object = $this->routerContainer->new($action, $route->getBindings());

        $args = func_get_args();
        array_shift($args);
        $result = $this->routerContainer->call(null, [ $object, '__invoke' ],
            $args + $route->getBindings()
        );

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
