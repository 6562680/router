<?php


namespace Gzhegow\Router;

use Gzhegow\Router\Vendor\Helper;
use Gzhegow\Router\Domain\Cors\Cors;
use Gzhegow\Router\Domain\Route\Route;
use Gzhegow\Router\Exceptions\RuntimeException;
use Gzhegow\Router\Domain\Route\RouteCollection;
use Gzhegow\Router\Domain\Configuration\Configuration;
use Gzhegow\Router\Domain\Configuration\PatternCollection;
use Gzhegow\Router\Service\RouteLoader\CallableRouteLoader;
use Gzhegow\Router\Service\RouteCompiler\CorsRouteCompiler;
use Gzhegow\Router\Service\RouteLoader\RouteLoaderInterface;
use Gzhegow\Router\Service\RouteLoader\BlueprintRouteLoader;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;
use Gzhegow\Router\Domain\Configuration\MiddlewareCollection;
use Gzhegow\Router\Service\RouteLoader\Logic\CaseRouteLoader;
use Gzhegow\Router\Exceptions\Runtime\UnexpectedValueException;
use Gzhegow\Router\Service\RouteCompiler\EndpointRouteCompiler;
use Gzhegow\Router\Service\RouteCompiler\RouteCompilerInterface;
use Gzhegow\Router\Service\RouteCompiler\SignatureRouteCompiler;
use Gzhegow\Router\Service\RouteCompiler\Logic\PipeRouteCompiler;
use Gzhegow\Router\Service\ActionProcessor\HandlerActionProcessor;
use Gzhegow\Router\Service\ActionProcessor\AsteriskActionProcessor;
use Gzhegow\Router\Service\ActionProcessor\ActionProcessorInterface;
use Gzhegow\Router\Service\ActionProcessor\Logic\CaseActionProcessor;
use Gzhegow\Router\Service\ActionProcessor\InvokableClassActionProcessor;
use Gzhegow\Router\Service\ActionProcessor\Callback\CallableStaticActionProcessor;


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

        $this->set(RouterContainerInterface::class, $this);

        $this->getRouterCache();

        $this->getRouteCollection();
        $this->getMiddlewareCollection();
        $this->getPatternCollection();

        $this->getRouteLoader();
        $this->getRouteCompiler();
        $this->getActionProcessor();
    }


    /**
     * @param string|object $objectOrClass
     * @param null|array    $parameters
     *
     * @return object
     */
    public function new($objectOrClass, array $parameters = null) : object
    {
        $class = null
            ?? ( is_object($objectOrClass) ? get_class($objectOrClass) : null )
            ?? ( is_string($objectOrClass) ? $objectOrClass : null );

        if (is_null($class)) {
            throw new InvalidArgumentException(
                [ 'Invalid ObjectOrClass: %s', $objectOrClass ]
            );
        }

        $autowiredParameters = $this->autowireConstructor($objectOrClass, $parameters);

        $object = new $class(...$autowiredParameters);

        return $object;
    }


    /**
     * @return RouteLoaderInterface
     */
    public function newRouteLoader() : RouteLoaderInterface
    {
        $routeLoader = new CaseRouteLoader();

        $routeLoader->addRouteLoader(new BlueprintRouteLoader($this));
        $routeLoader->addRouteLoader(new CallableRouteLoader());

        return $routeLoader;
    }

    /**
     * @return RouteCompilerInterface
     */
    public function newRouteCompiler() : RouteCompilerInterface
    {
        $routeCompiler = new PipeRouteCompiler();

        $routeCompiler->addRouteCompiler(new EndpointRouteCompiler($this->getPatternCollection()));
        $routeCompiler->addRouteCompiler(new SignatureRouteCompiler());
        $routeCompiler->addRouteCompiler(new CorsRouteCompiler());

        return $routeCompiler;
    }


    /**
     * @return ActionProcessorInterface
     */
    public function newActionProcessor() : ActionProcessorInterface
    {
        return new CaseActionProcessor([
            new HandlerActionProcessor($this), // HandlerInterface
            new InvokableClassActionProcessor($this), // $obj->__invoke()
            new AsteriskActionProcessor($this), // 'class@method'
            new CallableStaticActionProcessor($this), // 'class::method'
            // new CallableDynamicActionProcessor($this), // \Closure
        ]);
    }


    /**
     * @return RouterCacheInterface
     */
    public function getRouterCache() : RouterCacheInterface
    {
        if (! $this->has($id = RouterCacheInterface::class)) {
            $item = $this->configuration->getCache();
            $item = null
                ?? ( $item instanceof $id ? $item : null )
                ?? ( is_a($item, $id, true) ? $this->new($item) : null )
                ?? ( is_callable($item) ? $this->call(null, $item) : null )
                ?? null;

            $this->items[ $id ] = new RouterCache($item);
        }

        return $this->get($id);
    }


    /**
     * @return RouteLoaderInterface
     */
    public function getRouteLoader() : RouteLoaderInterface
    {
        if (! $this->has($id = RouteLoaderInterface::class)) {
            $item = $this->configuration->getRouteLoader();
            $item = null
                ?? ( $item instanceof $id ? $item : null )
                ?? ( is_a($item, $id, true) ? $this->new($item) : null )
                ?? ( is_callable($item) ? $this->call(null, $item) : null )
                ?? $this->newRouteLoader();

            $this->items[ $id ] = $item;
        }

        return $this->get($id);
    }

    /**
     * @return RouteCompilerInterface
     */
    public function getRouteCompiler() : RouteCompilerInterface
    {
        if (! $this->has($id = RouteCompilerInterface::class)) {
            $item = $this->configuration->getRouteCompiler();
            $item = null
                ?? ( $item instanceof $id ? $item : null )
                ?? ( is_a($item, $id, true) ? $this->new($item) : null )
                ?? ( is_callable($item) ? $this->call(null, $item) : null )
                ?? $this->newRouteCompiler();

            $this->items[ $id ] = $item;
        }

        return $this->get($id);
    }


    /**
     * @return ActionProcessorInterface
     */
    public function getActionProcessor() : ActionProcessorInterface
    {
        if (! $this->has($id = ActionProcessorInterface::class)) {
            $item = $this->configuration->getActionProcessor();
            $item = null
                ?? ( $item instanceof $id ? $item : null )
                ?? ( is_a($item, $id, true) ? $this->new($item) : null )
                ?? ( is_callable($item) ? $this->call(null, $item) : null )
                ?? $this->newActionProcessor();

            $this->items[ $id ] = $item;
        }

        return $this->get($id);
    }


    /**
     * @return RouteCollection
     */
    public function getRouteCollection() : RouteCollection
    {
        if (! $this->has($id = RouteCollection::class)) {
            $item = $this->configuration->getRouteCollection();
            $item = null
                ?? ( $item instanceof $id ? $item : null )
                ?? ( is_a($item, $id, true) ? $this->new($item) : null )
                ?? ( is_callable($item) ? $this->call(null, $item) : null )
                ?? new RouteCollection();

            $this->items[ $id ] = $item;
        }

        return $this->get($id);
    }


    /**
     * @return MiddlewareCollection
     */
    public function getMiddlewareCollection() : MiddlewareCollection
    {
        if (! $this->has($id = MiddlewareCollection::class)) {
            $item = $this->configuration->getMiddlewareCollection();
            $item = null
                ?? ( $item instanceof $id ? $item : null )
                ?? ( is_a($item, $id, true) ? $this->new($item) : null )
                ?? ( is_callable($item) ? $this->call(null, $item) : null )
                ?? new MiddlewareCollection($this->getActionProcessor());

            $this->items[ $id ] = $item;
        }

        return $this->get($id);
    }

    /**
     * @return PatternCollection
     */
    public function getPatternCollection() : PatternCollection
    {
        if (! $this->has($id = PatternCollection::class)) {
            $item = $this->configuration->getPatternCollection();
            $item = null
                ?? ( $item instanceof $id ? $item : null )
                ?? ( is_a($item, $id, true) ? $this->new($item) : null )
                ?? ( is_callable($item) ? $this->call(null, $item) : null )
                ?? new PatternCollection();

            $this->items[ $id ] = $item;
        }

        return $this->get(PatternCollection::class);
    }


    /**
     * @return null|Route
     */
    public function getRoute() : ?Route
    {
        return $this->get(Route::class);
    }

    /**
     * @return null|Cors
     */
    public function getCors() : ?Cors
    {
        return $this->get(Cors::class);
    }


    /**
     * @param string $id
     *
     * @return mixed
     */
    public function get(string $id)
    {
        if (array_key_exists($id, $this->items)) {
            $item = $this->items[ $id ];

        } elseif (( $container = $this->configuration->getContainer() )
            && $container->has($id)
        ) {
            $item = $container->get($id);

        } else {
            throw new UnexpectedValueException(
                [ 'Missing id: %s', $id ]
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
        return array_key_exists($id, $this->items)
            || ( ( $container = $this->configuration->getContainer() )
                && $container->has($id)
            );
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
     * @param string $id
     *
     * @return static
     */
    public function unset(string $id)
    {
        unset($this->items[ $id ]);

        return $this;
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
        $parameters = $parameters ?? [];

        $parametersAutowired = $this->autowireCallable($callable, $parameters);

        $result = null
            ?? ( $newthis ? \Closure::fromCallable($callable)->call($newthis, ...$parametersAutowired) : null )
            ?? call_user_func($callable, ...$parametersAutowired);

        return $result;
    }


    /**
     * @param string|object $objectOrClass
     * @param null|array    $parameters
     *
     * @return mixed
     */
    protected function autowireConstructor($objectOrClass, array $parameters = null) : array
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
    protected function autowireCallable(callable $callable, array $parameters = null) : array
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
    protected function autowireReflectionClass(\ReflectionClass $reflectionClass, array $parameters = null) : array
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
    protected function autowireReflectionFunction(\ReflectionFunctionAbstract $reflectionFunction, array $parameters = null) : array
    {
        $parameters = $parameters ?? [];

        $paramsAutowired = [];

        $paramsInt = [];
        $paramsString = [];
        foreach ( $parameters as $i => $param ) {
            if (is_int($i)) {
                $paramsInt[ $i ] = $param;

            } elseif (is_string($i) && strlen($i)) {
                if (class_exists($i) || interface_exists($i)) {
                    $paramsString[ $i ] = $param;

                } else {
                    $paramsString[ '$' . ltrim($i, '$') ] = $param;
                }
            }
        }

        foreach ( $reflectionFunction->getParameters() as $i => $rp ) {
            $rpName = $rp->getName();

            $rpTypeName = null;
            $rpType = $rp->getType();
            if ($rpType && ! $this->reflectionTypeIsBuiltin($rpType)) {
                if ($this->reflectionTypeIsNamed($rpType)
                    && ( 0
                        || class_exists($rpType->getName())
                        || interface_exists($rpType->getName())
                    )
                ) {
                    $rpTypeName = $rpType->getName();
                }
            }

            if (isset($paramsString[ $paramKey = '$' . $rpName ])) {
                $value = $paramsString[ $paramKey ];

                $paramsAutowired[ $i ] = $value;
                array_unshift($paramsInt, null);

            } elseif ($rpTypeName) {
                $instance = null;

                if (isset($paramsString[ $rpTypeName ])) {
                    $instance = $paramsString[ $rpTypeName ];

                } elseif (isset($paramsInt[ $i ])
                    && $paramsInt[ $i ] instanceof $rpTypeName
                ) {
                    $instance = $paramsInt[ $i ];
                    $paramsInt[ $i ] = null;

                } elseif ($this->has($rpTypeName)) {
                    $instance = $this->get($rpTypeName);
                }

                $paramsAutowired[ $i ] = $instance;
                array_unshift($paramsInt, null);

            } elseif (isset($paramsInt[ $i ])) {
                $paramsAutowired[ $i ] = $paramsInt[ $i ];
                $paramsInt[ $i ] = null;

            } elseif (! $rp->isVariadic()) {
                $paramsAutowired[ $i ] = null;
            }
        }

        $paramsAutowired += array_filter($paramsInt);

        return $paramsAutowired;
    }


    /**
     * @param callable $callable
     *
     * @return \ReflectionFunctionAbstract
     */
    protected function reflectCallable(callable $callable) : \ReflectionFunctionAbstract
    {
        try {
            if (is_object($callable)) {
                $reflectionFunctionAbstract = $callable instanceof \Closure
                    ? new \ReflectionFunction($callable)
                    : new \ReflectionMethod($callable, '__invoke');

            } else {
                if (is_array($callable)) {
                    $reflectionFunctionAbstract = new \ReflectionMethod(...$callable);

                } elseif ($callableString = Helper::filterCallableStringSemicolon($callable)) {
                    [ $class, $method ] = explode('::', $callableString, 2);

                    $reflectionFunctionAbstract = new \ReflectionMethod($class, $method);

                } elseif (is_string($callable) && function_exists($callable)) {
                    $reflectionFunctionAbstract = new \ReflectionFunction($callable);

                } else {
                    throw new UnexpectedValueException(
                        [ 'Unsupported callable for reflection: %s', $callable ]
                    );
                }
            }
        }
        catch ( \ReflectionException $e ) {
            throw new RuntimeException($e->getMessage(), null, $e);
        }

        return $reflectionFunctionAbstract;
    }


    /**
     * @param \ReflectionType $reflectionType
     *
     * @return bool
     */
    protected function reflectionTypeIsNamed(\ReflectionType $reflectionType) : bool
    {
        return is_a($reflectionType, 'ReflectionNamedType');
    }

    /**
     * @param \ReflectionType $reflectionType
     *
     * @return bool
     */
    protected function reflectionTypeIsBuiltin(\ReflectionType $reflectionType) : bool
    {
        $isBuiltIn = false;

        try {
            $isBuiltIn = $reflectionType->{'isBuiltin'}();
        }
        catch ( \Throwable $e ) {
        }

        return $isBuiltIn;
    }
}
