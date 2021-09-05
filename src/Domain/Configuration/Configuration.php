<?php


namespace Gzhegow\Router\Domain\Configuration;

use Gzhegow\Router\Domain\Factory\RouterFactory;
use Gzhegow\Router\Domain\Loader\ChainRouteLoader;
use Gzhegow\Router\Domain\Handler\HandlerInterface;
use Gzhegow\Router\Domain\Loader\FilePhpRouteLoader;
use Gzhegow\Router\Domain\Container\RouterContainer;
use Gzhegow\Router\Domain\Loader\CallableRouteLoader;
use Gzhegow\Router\Domain\Loader\RouteLoaderInterface;
use Gzhegow\Router\Domain\Compiler\PatternRouteCompiler;
use Gzhegow\Router\Domain\Factory\RouterFactoryInterface;
use Gzhegow\Router\Domain\Compiler\RouteCompilerInterface;
use Gzhegow\Router\Domain\Container\RouterContainerInterface;
use Gzhegow\Router\Domain\Processor\Action\ChainActionProcessor;
use Gzhegow\Router\Domain\Handler\Middleware\MiddlewareInterface;
use Gzhegow\Router\Domain\Processor\Action\ActionProcessorInterface;
use Gzhegow\Router\Domain\Processor\Middleware\ChainMiddlewareProcessor;
use Gzhegow\Router\Domain\Processor\Middleware\MiddlewareProcessorInterface;


/**
 * Configuration
 */
class Configuration
{
    const METHOD_CLI     = 'CLI';
    const METHOD_CONNECT = 'CONNECT';
    const METHOD_DELETE  = 'DELETE';
    const METHOD_GET     = 'GET';
    const METHOD_HEAD    = 'HEAD';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_PATCH   = 'PATCH';
    const METHOD_POST    = 'POST';
    const METHOD_PURGE   = 'PURGE';
    const METHOD_PUT     = 'PUT';
    const METHOD_SOCK    = 'SOCK';
    const METHOD_TRACE   = 'TRACE';

    const THE_METHOD_LIST = [
        self::METHOD_CLI => true,

        self::METHOD_HEAD    => true,
        self::METHOD_OPTIONS => true,
        self::METHOD_GET     => true,
        self::METHOD_POST    => true,
        self::METHOD_PUT     => true,
        self::METHOD_PATCH   => true,
        self::METHOD_DELETE  => true,
        self::METHOD_PURGE   => true,
        self::METHOD_TRACE   => true,
        self::METHOD_CONNECT => true,

        self::METHOD_SOCK => true,
    ];


    /**
     * @var RouterContainerInterface
     */
    protected $routerContainer;
    /**
     * @var RouterFactoryInterface
     */
    protected $routerFactory;

    /**
     * @var RouteCompilerInterface
     */
    protected $routeCompiler;
    /**
     * @var RouteLoaderInterface
     */
    protected $routeLoader;

    /**
     * @var MiddlewareProcessorInterface
     */
    protected $middlewareProcessor;
    /**
     * @var ActionProcessorInterface
     */
    protected $actionProcessor;

    /**
     * @var MiddlewareCollection
     */
    protected $middlewareCollection;
    /**
     * @var MiddlewareGroupCollection
     */
    protected $middlewareGroupCollection;
    /**
     * @var PatternCollection
     */
    protected $patternCollection;
    /**
     * @var SeparatorCollection
     */
    protected $separatorCollection;


    /**
     * Constructor
     *
     * @param null|RouterContainerInterface     $routerContainer
     * @param null|RouterFactoryInterface       $routerFactory
     *
     * @param null|RouteLoaderInterface         $routeLoader
     * @param null|RouteCompilerInterface       $routeCompiler
     *
     * @param null|MiddlewareProcessorInterface $middlewareProcessor
     * @param null|ActionProcessorInterface     $actionProcessor
     */
    public function __construct(
        RouterContainerInterface $routerContainer = null,
        RouterFactoryInterface $routerFactory = null,

        RouteLoaderInterface $routeLoader = null,
        RouteCompilerInterface $routeCompiler = null,

        MiddlewareProcessorInterface $middlewareProcessor = null,
        ActionProcessorInterface $actionProcessor = null
    )
    {
        $routerContainer = $routerContainer
            ?? new RouterContainer(null);

        $routerFactory = $routerFactory
            ?? new RouterFactory($this);

        $routeLoader = $routeLoader
            ?? new ChainRouteLoader([
                new CallableRouteLoader(),
                new FilePhpRouteLoader(),
            ]);

        $routeCompiler = $routeCompiler
            ?? new PatternRouteCompiler($this);

        $middlewareProcessor = $middlewareProcessor
            ?? new ChainMiddlewareProcessor([
                // new CallableMiddlewareProcessor(),
            ]);

        $actionProcessor = $actionProcessor
            ?? new ChainActionProcessor([
                // new AsteriskActionProcessor($this),
                // new CallableActionProcessor(),
            ]);


        $this->routerContainer = $routerContainer;
        $this->routerFactory = $routerFactory;

        $this->routeLoader = $routeLoader;
        $this->routeCompiler = $routeCompiler;

        $this->middlewareProcessor = $middlewareProcessor;
        $this->actionProcessor = $actionProcessor;

        $this->middlewareCollection = new MiddlewareCollection($middlewareProcessor);
        $this->middlewareGroupCollection = new MiddlewareGroupCollection($middlewareProcessor);
        $this->patternCollection = new PatternCollection();
        $this->separatorCollection = new SeparatorCollection();
    }


    /**
     * @return RouterContainerInterface
     */
    public function getRouterContainer() : RouterContainerInterface
    {
        return $this->routerContainer;
    }

    /**
     * @return RouterFactoryInterface
     */
    public function getRouterFactory() : RouterFactoryInterface
    {
        return $this->routerFactory;
    }


    /**
     * @return RouteLoaderInterface
     */
    public function getRouteLoader() : RouteLoaderInterface
    {
        return $this->routeLoader;
    }

    /**
     * @return RouteCompilerInterface
     */
    public function getRouteCompiler() : RouteCompilerInterface
    {
        return $this->routeCompiler;
    }


    /**
     * @return MiddlewareProcessorInterface
     */
    public function getMiddlewareProcessor() : MiddlewareProcessorInterface
    {
        return $this->middlewareProcessor;
    }

    /**
     * @return ActionProcessorInterface
     */
    public function getActionProcessor() : ActionProcessorInterface
    {
        return $this->actionProcessor;
    }


    /**
     * @return MiddlewareCollection
     */
    public function getMiddlewareCollection() : MiddlewareCollection
    {
        return $this->middlewareCollection;
    }

    /**
     * @return MiddlewareGroupCollection
     */
    public function getMiddlewareGroupCollection() : MiddlewareGroupCollection
    {
        return $this->middlewareGroupCollection;
    }

    /**
     * @return SeparatorCollection
     */
    public function getSeparatorCollection() : SeparatorCollection
    {
        return $this->separatorCollection;
    }

    /**
     * @return PatternCollection
     */
    public function getPatternCollection() : PatternCollection
    {
        return $this->patternCollection;
    }


    /**
     * @param string|mixed $routeMethod
     *
     * @return null|string
     */
    public function filterMethod($routeMethod) : ?string
    {
        return isset(static::THE_METHOD_LIST[ $routeMethod ]);
    }


    /**
     * @param string|callable|HandlerInterface|mixed $action
     *
     * @return null|string
     */
    public function filterAction($action) : ?string
    {
        return null
            ?? $this->actionProcessor->supportsAction($action);
    }

    /**
     * @param string|object|callable|MiddlewareInterface|mixed $middleware
     *
     * @return null|string
     */
    public function filterMiddleware($middleware) : ?string
    {
        return null
            ?? $this->middlewareProcessor->supportsMiddleware($middleware);
    }
}
