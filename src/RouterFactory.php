<?php


namespace Gzhegow\Router;

use Gzhegow\Router\Domain\Handler\HandlerInterface;
use Gzhegow\Router\Domain\Handler\Action\GenericAction;
use Gzhegow\Router\Service\RouteLoader\FilePhpRouteLoader;
use Gzhegow\Router\Service\RouteCompiler\CorsRouteCompiler;
use Gzhegow\Router\Service\RouteLoader\CallableRouteLoader;
use Gzhegow\Router\Service\RouteLoader\RouteLoaderInterface;
use Gzhegow\Router\Service\RouteLoader\BlueprintRouteLoader;
use Gzhegow\Router\Service\RouteCompiler\PatternRouteCompiler;
use Gzhegow\Router\Domain\Handler\Middleware\GenericMiddleware;
use Gzhegow\Router\Service\RouteCompiler\RouteCompilerInterface;
use Gzhegow\Router\Domain\Handler\Middleware\MiddlewareInterface;
use Gzhegow\Router\Service\ActionProcessor\HandlerActionProcessor;
use Gzhegow\Router\Service\RouteLoader\Collection\CaseRouteLoader;
use Gzhegow\Router\Service\ActionProcessor\AsteriskActionProcessor;
use Gzhegow\Router\Service\ActionProcessor\CallableActionProcessor;
use Gzhegow\Router\Service\ActionProcessor\ActionProcessorInterface;
use Gzhegow\Router\Service\RouteCompiler\Collection\PipeRouteCompiler;
use Gzhegow\Router\Service\ActionProcessor\InvokableClassActionProcessor;
use Gzhegow\Router\Service\ActionProcessor\Collection\CaseActionProcessor;


/**
 * RouterFactory
 */
class RouterFactory implements RouterFactoryInterface
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
     * @return RouteLoaderInterface
     */
    public function newRouteLoader() : RouteLoaderInterface
    {
        return new CaseRouteLoader([
            new BlueprintRouteLoader($this->routerContainer),
            new FilePhpRouteLoader(),
            new CallableRouteLoader(),
        ]);
    }

    /**
     * @return RouteCompilerInterface
     */
    public function newRouteCompiler() : RouteCompilerInterface
    {
        return new PipeRouteCompiler([
            new PatternRouteCompiler($this->routerContainer->getPatternCollection()),
            new CorsRouteCompiler($this->routerContainer->getCorsMiddleware()),
        ]);
    }


    /**
     * @return ActionProcessorInterface
     */
    public function newActionProcessor() : ActionProcessorInterface
    {
        return new CaseActionProcessor([
            new HandlerActionProcessor($this->routerContainer),
            new InvokableClassActionProcessor($this->routerContainer),
            new AsteriskActionProcessor($this->routerContainer),
            new CallableActionProcessor($this->routerContainer),
        ]);
    }


    /**
     * @param mixed $action
     *
     * @return HandlerInterface
     */
    public function newAction($action) : HandlerInterface
    {
        $instance = new GenericAction($action,
            $this->routerContainer->getActionProcessor()
        );

        return $instance;
    }

    /**
     * @param mixed                 $middleware
     * @param null|HandlerInterface $next
     *
     * @return MiddlewareInterface
     */
    public function newMiddleware($middleware, HandlerInterface $next = null) : MiddlewareInterface
    {
        $instance = new GenericMiddleware($middleware,
            $this->routerContainer->getActionProcessor()
        );

        if (null !== $next) {
            $instance->setNext($next);
        }

        return $instance;
    }
}
