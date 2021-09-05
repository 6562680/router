<?php


namespace Gzhegow\Router\Domain\Processor\Action;

use Gzhegow\Router\Domain\Factory\RouterFactoryInterface;


/**
 * AsteriskActionProcessor
 */
class AsteriskActionProcessor implements ActionProcessorInterface
{
    /**
     * @var RouterFactoryInterface
     */
    protected $routerFactory;


    /**
     * Constructor
     *
     * @param RouterFactoryInterface $routerFactory
     */
    public function __construct(RouterFactoryInterface $routerFactory)
    {
        $this->routerFactory = $routerFactory;
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
        $result = null;

        // [ $actionClass, $method ] = explode('@', $action);
        //
        // $route = $this->getRouteCurrent();
        //
        // $actionObject = $this->newAutowired($actionClass, $route->getBindings());
        //
        // $result = $this->callAutowired(null, [ $actionObject, $method ], $route->getBindings());

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
        catch ( \ReflectionException ) {
        }

        return null;
    }


    /**
     * @param callable $action
     *
     * @return bool
     */
    public function supportsAction($action) : bool
    {
        return $this->filterActionAsterisk($action);
    }
}
