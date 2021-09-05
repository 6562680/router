<?php


namespace Gzhegow\Router\Domain\Factory;

use Gzhegow\Router\Domain\Handler\HandlerInterface;
use Gzhegow\Router\Domain\Configuration\Configuration;
use Gzhegow\Router\Domain\Handler\Action\GenericAction;
use Gzhegow\Router\Exceptions\Logic\InvalidArgumentException;
use Gzhegow\Router\Domain\Handler\Middleware\GenericMiddleware;
use Gzhegow\Router\Domain\Handler\Middleware\MiddlewareInterface;


/**
 * RouterFactory
 */
class RouterFactory implements RouterFactoryInterface
{
    /**
     * @var Configuration
     */
    protected $configuration;


    /**
     * Constructor
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }


    /**
     * @param string|callable|HandlerInterface|mixed $action
     *
     * @return HandlerInterface
     */
    public function newAction($action) : HandlerInterface
    {
        $action = null
            ?? ( $action instanceof HandlerInterface ? $action : null )
            ?? new GenericAction($action, $this->configuration->getActionProcessor());

        return $action;
    }

    /**
     * @param string|object|callable|MiddlewareInterface|mixed $middleware
     * @param null|HandlerInterface                            $next
     *
     * @return MiddlewareInterface
     */
    public function newMiddleware($middleware, HandlerInterface $next = null) : MiddlewareInterface
    {
        $middleware = null
            ?? ( $middleware instanceof MiddlewareInterface ? $middleware : null )
            ?? new GenericMiddleware($middleware, $this->configuration->getMiddlewareProcessor());

        if (null !== $next) {
            $middleware->setNext($next);
        }

        return $middleware;
    }


    /**
     * @param string|object $objectOrClass
     * @param null|array    $parameters
     *
     * @return object
     */
    public function newObject($objectOrClass, array $parameters = null) : object
    {
        $class = null
            ?? ( is_object($objectOrClass) ? get_class($objectOrClass) : null )
            ?? ( is_string($objectOrClass) ? $objectOrClass : null );

        if (is_null($class)) {
            throw new InvalidArgumentException(
                [ 'Invalid ObjectOrClass: %s', $objectOrClass ]
            );
        }

        $routerContainer = $this->configuration->getRouterContainer();

        $autowiredParameters = $routerContainer->autowireConstructor($objectOrClass, $parameters);

        $object = new $class(...$autowiredParameters);

        return $object;
    }
}
