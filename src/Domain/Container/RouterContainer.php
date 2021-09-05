<?php


namespace Gzhegow\Router\Domain\Container;

use Gzhegow\Router\Utils;
use Psr\Container\ContainerInterface;
use Gzhegow\Router\Exceptions\RuntimeException;
use Gzhegow\Router\Exceptions\Runtime\OutOfBoundsException;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;


/**
 * RouterContainer
 */
class RouterContainer implements RouterContainerInterface
{
    /**
     * @var null|ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $items = [];


    /**
     * Constructor
     *
     * @param null|ContainerInterface $container
     */
    public function __construct(?ContainerInterface $container)
    {
        $this->container = $container;
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
        catch ( \Throwable ) {
        }

        return $isBuiltIn;
    }


    /**
     * @param string $id
     *
     * @return mixed
     */
    public function get(string $id)
    {
        if (isset($this->items[ $id ])) {
            return $this->items[ $id ];
        }

        if ($this->container) {
            return $this->container->get($id);
        }

        throw new OutOfBoundsException(
            [ 'Item not found: %s', $id ]
        );
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has(string $id) : bool
    {
        return false
            || isset($this->items[ $id ])
            || ( $this->container && $this->container->has($id) );
    }


    /**
     * @param string $id
     * @param mixed  $item
     *
     * @return static
     */
    public function set(string $id, $item)
    {
        $this->items[ $id ] = $item;

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
        $result = $this->callAutowired($newthis, $callable, $parameters);

        return $result;
    }


    /**
     * @param string|object $objectOrClass
     * @param null|array    $parameters
     *
     * @return mixed
     */
    public function autowireConstructor($objectOrClass, array $parameters = null) : array
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
    public function autowireCallable(callable $callable, array $parameters = null) : array
    {
        $reflectionFunctionAbstract = $this->reflectCallable($callable);

        $result = $this->autowireReflectionFunction($reflectionFunctionAbstract, $parameters);

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
     * @param \ReflectionClass $reflectionClass
     * @param null|array       $parameters
     *
     * @return array
     */
    protected function autowireReflectionClass(\ReflectionClass $reflectionClass, array $parameters = null) : array
    {
        $result = static::autowireReflectionFunction($reflectionClass->getConstructor(), $parameters);

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

            $rpTypeName = null;
            $rpType = $rp->getType();
            if ($rpType && ! $this->isReflectionTypeBuiltin($rpType)) {
                if (is_a($rpType, 'ReflectionNamedType')
                    && ( false
                        || class_exists($rpType->getName())
                        || interface_exists($rpType->getName())
                    )
                ) {
                    $rpTypeName = $rpType->getName();
                }
            }

            if ($rpTypeName && isset($paramsString[ $rpTypeName ])) {
                $value = $paramsString[ $rpTypeName ];

                $paramsAutowired[ $i ] = $value;
                array_unshift($paramsInt, $value);

            } elseif (isset($paramsString[ '$' . $rpName ])) {
                $value = $paramsString[ '$' . $rpName ];

                $paramsAutowired[ $i ] = $value;
                array_unshift($paramsInt, $value);

            } elseif (isset($parameters[ $i ])) {
                $paramsAutowired[ $i ] = $parameters[ $i ];

            } else {
                if ($rpTypeName && $this->has($rpTypeName)) {
                    $instance = $this->get($rpTypeName);

                    $paramsAutowired[ $i ] = $instance;
                    $paramsString[ $rpName ] = $instance;
                    array_unshift($paramsInt, $instance);

                } else {
                    $paramsAutowired[ $i ] = null;
                }
            }
        }

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
            if ($closure = Utils::filterClosure($function)) {
                $reflectionFunctionAbstract = new \ReflectionFunction($closure);

            } elseif (is_callable($function)) {
                if (is_object($function)) {
                    $reflectionFunctionAbstract = new \ReflectionMethod($function, '__invoke');

                } elseif (is_array($function)) {
                    $reflectionFunctionAbstract = new \ReflectionMethod(...$function);

                } elseif ($callableString = Utils::filterCallableStringSemicolon($function)) {
                    [ $class, $method ] = explode('::', $callableString, 2);

                    $reflectionFunctionAbstract = new \ReflectionMethod($class, $method);

                } else {
                    $reflectionFunctionAbstract = new \ReflectionFunction($function);
                }
            } else {
                $reflectionFunctionAbstract = new \ReflectionFunction($function);
            }
        }
        catch ( \ReflectionException $e ) {
            throw new RuntimeException($e->getMessage(), null, $e);
        }

        return $reflectionFunctionAbstract;
    }
}
