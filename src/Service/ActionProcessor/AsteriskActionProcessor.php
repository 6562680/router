<?php


namespace Gzhegow\Router\Service\ActionProcessor;

use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\RouterContainerInterface;
use Gzhegow\Router\Domain\Route\RouteAwareInterface;


/**
 * AsteriskActionProcessor
 */
class AsteriskActionProcessor implements
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

        [ $actionClass, $actionMethod ] = explode('@', $action);

        $actionObject = $this->routerContainer->new($actionClass, $route->getBindings());

        $callable = [ $actionObject, $actionMethod ];

        $args = func_get_args();
        array_shift($args);
        $result = $this->routerContainer->call(null, $callable,
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
     * @param string|mixed $callableString
     *
     * @return null|string
     */
    protected function filterActionAsterisk($callableString) : ?string
    {
        if (! is_string($callableString)) {
            return null;
        }

        $classMethod = explode('@', $callableString);
        if (count($classMethod) !== 2) {
            return null;
        }

        try {
            $rm = new \ReflectionMethod(...$classMethod);

            if (! $rm->isPublic()
                || $rm->isStatic()
                || $rm->isAbstract()
            ) {
                return null;
            }

            return $callableString;
        }
        catch ( \ReflectionException $e ) {
        }

        return null;
    }


    /**
     * @param string $action
     *
     * @return bool
     */
    public function supportsAction($action) : bool
    {
        return (bool) $this->filterActionAsterisk($action);
    }
}
