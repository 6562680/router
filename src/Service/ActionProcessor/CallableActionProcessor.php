<?php


namespace Gzhegow\Router\Service\ActionProcessor;

use Gzhegow\Router\Vendor\Helper;
use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\RouterContainerInterface;
use Gzhegow\Router\Domain\Route\RouteAwareInterface;


/**
 * CallableActionProcessor
 */
class CallableActionProcessor implements
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
     * @param callable $action
     * @param mixed    $payload
     * @param mixed    ...$arguments
     *
     * @return null|int|mixed
     */
    public function processAction($action, $payload, ...$arguments)
    {
        $route = $this->getRoute();

        $args = func_get_args();
        array_shift($args);
        $result = $this->routerContainer->call(null, $action,
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
     * > skips \Closure && __invoke()
     * > skips array callables with dynamic object as context
     *
     * @param callable $action
     *
     * @return bool
     */
    public function supportsAction($action) : bool
    {
        if (is_object($action)) {
            return false;
        }

        return is_callable($action)
            && ! Helper::filterCallableArrayPublic($action);
    }
}
