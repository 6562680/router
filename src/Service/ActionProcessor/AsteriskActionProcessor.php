<?php


namespace Gzhegow\Router\Service\ActionProcessor;

use Gzhegow\Router\RouterContainerInterface;


/**
 * AsteriskActionProcessor
 */
class AsteriskActionProcessor implements
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

        [ $actionClass, $actionMethod ] = explode('@', $action);

        $actionObject = $this->routerContainer->new($actionClass, $args);

        $result = $this->routerContainer->call(null, [ $actionObject, $actionMethod ], $args);

        return $result;
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
