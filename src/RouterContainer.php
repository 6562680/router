<?php


namespace Gzhegow\Router;

use Gzhegow\Router\Vendor\Helper;
use Psr\Container\ContainerInterface;
use Gzhegow\Router\Domain\Cors\CorsMiddleware;
use Gzhegow\Router\Exceptions\RuntimeException;
use Gzhegow\Router\Domain\Route\RouteCollection;
use Gzhegow\Router\Domain\Configuration\Configuration;
use Gzhegow\Router\Domain\Configuration\PatternCollection;
use Gzhegow\Router\Service\RouteLoader\RouteLoaderInterface;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;
use Gzhegow\Router\Domain\Configuration\MiddlewareCollection;
use Gzhegow\Router\Exceptions\Runtime\UnexpectedValueException;
use Gzhegow\Router\Service\RouteCompiler\RouteCompilerInterface;
use Gzhegow\Router\Service\ActionProcessor\ActionProcessorInterface;


/**
 * RouterContainer
 */
class RouterContainer implements RouterContainerInterface
{
    /**
     * @var Configuration
     */
    protected $configuration;


    /**
     * @var array
     */
    protected $items = [];


    /**
     * Constructor
     *
     * @param null|Configuration $configuration
     */
    public function __construct(?Configuration $configuration)
    {
        $configuration = $configuration ?? new Configuration();

        $this->configuration = $configuration;

        $this->set(ContainerInterface::class, $this);
        $this->set(RouterContainerInterface::class, $this);

        $this->getMiddlewareCollection();
        $this->getPatternCollection();

        $this->getRouterFactory();
        $this->getRouteLoader();
        $this->getRouteCompiler();
        $this->getActionProcessor();
        $this->getCorsMiddleware();
    }


    /**
     * @param string|object $objectOrClass
     * @param null|array    $bindings
     *
     * @return object
     */
    public function new($objectOrClass, array $bindings = null) : object
    {
        $class = null
            ?? ( is_object($objectOrClass) ? get_class($objectOrClass) : null )
            ?? ( is_string($objectOrClass) ? $objectOrClass : null );

        if (is_null($class)) {
            throw new InvalidArgumentException(
                [ 'Invalid ObjectOrClass: %s', $objectOrClass ]
            );
        }

        $autowiredParameters = $this->autowireConstructor($objectOrClass, $bindings);

        $object = new $class(...$autowiredParameters);

        return $object;
    }


    /**
     * @return RouterFactoryInterface
     */
    public function getRouterFactory() : RouterFactoryInterface
    {
        if (! $this->has(RouterFactoryInterface::class)) {
            $this->items[ RouterFactoryInterface::class ] = null
                ?? $this->configuration->getRouterFactory()
                ?? new RouterFactory($this);
        }

        return $this->get(RouterFactoryInterface::class);
    }

    /**
     * @return null|RouterCacheInterface
     */
    public function getRouterCache() : ?RouterCacheInterface
    {
        if (! $this->has(RouterCacheInterface::class)) {
            $this->items[ RouterCacheInterface::class ] = null
                ?? new RouterCache($this->configuration->getCache());
        }

        return $this->get(RouterCacheInterface::class);
    }


    /**
     * @return RouteLoaderInterface
     */
    public function getRouteLoader() : RouteLoaderInterface
    {
        if (! $this->has(RouteLoaderInterface::class)) {
            $this->items[ RouteLoaderInterface::class ] = null
                ?? $this->configuration->getRouteLoader()
                ?? $this->getRouterFactory()->newRouteLoader();
        }

        return $this->get(RouteLoaderInterface::class);
    }

    /**
     * @return RouteCompilerInterface
     */
    public function getRouteCompiler() : RouteCompilerInterface
    {
        if (! $this->has(RouteCompilerInterface::class)) {
            $this->items[ RouteCompilerInterface::class ] = null
                ?? $this->configuration->getRouteCompiler()
                ?? $this->getRouterFactory()->newRouteCompiler();
        }

        return $this->get(RouteCompilerInterface::class);
    }


    /**
     * @return ActionProcessorInterface
     */
    public function getActionProcessor() : ActionProcessorInterface
    {
        if (! $this->has(ActionProcessorInterface::class)) {
            $this->items[ ActionProcessorInterface::class ] = null
                ?? $this->configuration->getActionProcessor()
                ?? $this->getRouterFactory()->newActionProcessor();
        }

        return $this->get(ActionProcessorInterface::class);
    }


    /**
     * @return mixed
     */
    public function getCorsMiddleware()
    {
        if (! $this->has(CorsMiddleware::class)) {
            $this->items[ CorsMiddleware::class ] = null
                ?? $this->configuration->getCorsMiddleware()
                ?? CorsMiddleware::class;
        }

        $corsMiddleware = $this->get(CorsMiddleware::class);

        $actionProcessor = $this->getActionProcessor();

        if (! $actionProcessor->supportsAction($corsMiddleware)) {
            throw new InvalidArgumentException(
                [ 'Invalid CORS middleware: %s', $corsMiddleware ]
            );
        }

        return $corsMiddleware;
    }


    /**
     * @return RouteCollection
     */
    public function getRouteCollection() : RouteCollection
    {
        return $this->get(RouteCollection::class);
    }


    /**
     * @return MiddlewareCollection
     */
    public function getMiddlewareCollection() : MiddlewareCollection
    {
        if (! $this->has(MiddlewareCollection::class)) {
            $this->items[ MiddlewareCollection::class ] = null
                ?? new MiddlewareCollection(
                    $this->getActionProcessor()
                );
        }

        return $this->get(MiddlewareCollection::class);
    }

    /**
     * @return PatternCollection
     */
    public function getPatternCollection() : PatternCollection
    {
        if (! $this->has(PatternCollection::class)) {
            $this->items[ PatternCollection::class ] = null
                ?? new PatternCollection();
        }

        return $this->get(PatternCollection::class);
    }


    /**
     * @param string $id
     *
     * @return mixed
     */
    public function get(string $id)
    {
        $container = $this->configuration->getContainer();

        if (array_key_exists($id, $this->items)) {
            $item = $this->items[ $id ];

        } elseif ($container && $container->has($id)) {
            $item = $container->get($id);

        } else {
            throw new UnexpectedValueException(
                [ 'Missing Container id: %s', $id ]
            );
        }

        return $item;
    }


    /**
     * @param string $id
     *
     * @return bool
     */
    public function has(string $id) : bool
    {
        $container = $this->configuration->getContainer();

        return array_key_exists($id, $this->items)
            || $container && $container->has($id);
    }


    /**
     * @param string $id
     * @param mixed  $value
     *
     * @return static
     */
    public function set(string $id, $value)
    {
        $this->items[ $id ] = $value;

        return $this;
    }


    /**
     * @param \ReflectionType $reflectionType
     *
     * @return bool
     */
    protected function isReflectionTypeBuiltin(\ReflectionType $reflectionType) : bool
    {
        $isBuiltIn = false;

        try {
            $isBuiltIn = $reflectionType->{'isBuiltin'}();
        }
        catch ( \Throwable $e ) {
        }

        return $isBuiltIn;
    }


    /**
     * @param null|object            $newthis
     * @param string|object|callable $callable
     * @param null|array             $parameters
     *
     * @return mixed
     */
    public function call(?object $newthis, $callable, array $parameters = null)
    {
        $result = $this->callAutowired($newthis, $callable, $parameters);

        return $result;
    }


    /**
     * @param null|object $newthis
     * @param callable    $function
     * @param null|array  $parameters
     *
     * @return mixed
     */
    protected function callAutowired(?object $newthis, callable $function, array $parameters = null)
    {
        $parameters = $parameters ?? [];

        $parametersAutowired = $this->autowireCallable($function, $parameters);

        $result = null
            ?? ( $newthis ? \Closure::fromCallable($function)->call($newthis, ...$parametersAutowired) : null )
            ?? call_user_func($function, ...$parametersAutowired);

        return $result;
    }


    /**
     * @param string|object $objectOrClass
     * @param null|array    $parameters
     *
     * @return mixed
     */
    protected function autowireConstructor($objectOrClass,
        array $parameters = null
    ) : array
    {
        $class = null
            ?? ( is_object($objectOrClass) ? get_class($objectOrClass) : null )
            ?? ( is_string($objectOrClass) ? $objectOrClass : null );

        if (is_null($class)) {
            throw new InvalidArgumentException(
                [ 'Invalid ObjectOrClass: %s', $objectOrClass ]
            );
        }

        try {
            $reflectionClass = new \ReflectionClass($class);
        }
        catch ( \ReflectionException $e ) {
            throw new RuntimeException($e->getMessage(), null, $e);
        }

        $result = $this->autowireReflectionClass($reflectionClass, $parameters);

        return $result;
    }

    /**
     * @param callable   $callable
     * @param null|array $parameters
     *
     * @return mixed
     */
    protected function autowireCallable(callable $callable,
        array $parameters = null
    ) : array
    {
        $reflectionFunctionAbstract = $this->reflectCallable($callable);

        $result = $this->autowireReflectionFunction($reflectionFunctionAbstract, $parameters);

        return $result;
    }


    /**
     * @param \ReflectionClass $reflectionClass
     * @param null|array       $parameters
     *
     * @return array
     */
    protected function autowireReflectionClass(\ReflectionClass $reflectionClass,
        array $parameters = null
    ) : array
    {
        $result = [];

        if ($constructor = $reflectionClass->getConstructor()) {
            $result = static::autowireReflectionFunction($constructor, $parameters);
        }

        return $result;
    }

    /**
     * @param \ReflectionFunctionAbstract $reflectionFunction
     * @param null|array                  $parameters
     *
     * @return array
     */
    protected function autowireReflectionFunction(\ReflectionFunctionAbstract $reflectionFunction,
        array $parameters = null
    ) : array
    {
        $parameters = $parameters ?? [];

        $paramsAutowired = [];

        $paramsInt = [];
        $paramsString = [];
        foreach ( $parameters as $i => $param ) {
            if (is_int($i)) {
                $paramsInt[ $i ] = $param;

            } elseif (strlen($i)) {
                if (! ( class_exists($i) || interface_exists($i) )) {
                    $paramsString[ '$' . ltrim($i, '$') ] = $param;
                } else {
                    $paramsString[ $i ] = $param;
                }
            }
        }

        foreach ( $reflectionFunction->getParameters() as $i => $rp ) {
            $rpName = $rp->getName();

            if (isset($paramsInt[ $i ])) {
                $paramsAutowired[ $i ] = $paramsInt[ $i ];
                $paramsInt[ $i ] = null;

            } else {
                $rpTypeName = null;
                $rpType = $rp->getType();
                if ($rpType && ! $this->isReflectionTypeBuiltin($rpType)) {
                    if (is_a($rpType, 'ReflectionNamedType')
                        && ( class_exists($rpType->getName()) || interface_exists($rpType->getName()) )
                    ) {
                        $rpTypeName = $rpType->getName();
                    }
                }

                if ($rpTypeName && isset($paramsString[ $rpTypeName ])) {
                    $value = $paramsString[ $rpTypeName ];

                    $paramsAutowired[ $i ] = $value;
                    array_unshift($paramsInt, null);

                } elseif (isset($paramsString[ $paramKey = '$' . $rpName ])) {
                    $value = $paramsString[ $paramKey ];

                    $paramsAutowired[ $i ] = $value;
                    array_unshift($paramsInt, null);

                } elseif ($rpTypeName && $this->has($rpTypeName)) {
                    $instance = $this->get($rpTypeName);

                    $paramsAutowired[ $i ] = $instance;
                    $paramsString[ $rpName ] = $instance;
                    array_unshift($paramsInt, null);

                } else {
                    $paramsAutowired[ $i ] = null;
                }
            }
        }

        $paramsAutowired += array_filter($paramsInt);

        return $paramsAutowired;
    }


    /**
     * @param callable $function
     *
     * @return \ReflectionFunctionAbstract
     */
    protected function reflectCallable(callable $function) : \ReflectionFunctionAbstract
    {
        try {
            if (is_object($function)) {
                $reflectionFunctionAbstract = $function instanceof \Closure
                    ? new \ReflectionFunction($function)
                    : new \ReflectionMethod($function, '__invoke');

            } else {
                if (is_array($function)) {
                    $reflectionFunctionAbstract = new \ReflectionMethod(...$function);

                } elseif ($callableString = Helper::filterCallableStringSemicolon($function)) {
                    [ $class, $method ] = explode('::', $callableString, 2);

                    $reflectionFunctionAbstract = new \ReflectionMethod($class, $method);

                } else {
                    $reflectionFunctionAbstract = new \ReflectionFunction($function);
                }
            }
        }
        catch ( \ReflectionException $e ) {
            throw new RuntimeException($e->getMessage(), null, $e);
        }

        return $reflectionFunctionAbstract;
    }
}
