<?php


namespace Gzhegow\Router\Domain\Configuration;

use Psr\SimpleCache\CacheInterface;
use Psr\Container\ContainerInterface;
use Gzhegow\Router\Domain\Route\RouteCollection;
use Gzhegow\Router\Service\RouteLoader\RouteLoaderInterface;
use Gzhegow\Router\Service\RouteCompiler\RouteCompilerInterface;
use Gzhegow\Router\Service\ActionProcessor\ActionProcessorInterface;


/**
 * Configuration
 */
class Configuration
{
    /**
     * @var ContainerInterface|callable
     */
    protected $container;
    /**
     * @var CacheInterface|callable
     */
    protected $cache;

    /**
     * @var RouteLoaderInterface|callable
     */
    protected $routeLoader;
    /**
     * @var RouteCompilerInterface|callable
     */
    protected $routeCompiler;

    /**
     * @var ActionProcessorInterface|callable
     */
    protected $actionProcessor;

    /**
     * @var RouteCollection|callable
     */
    protected $routeCollection;

    /**
     * @var MiddlewareCollection|callable
     */
    protected $middlewareCollection;
    /**
     * @var PatternCollection|callable
     */
    protected $patternCollection;


    /**
     * @return null|ContainerInterface|callable
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return null|CacheInterface|callable
     */
    public function getCache()
    {
        return $this->cache;
    }


    /**
     * @return null|RouteLoaderInterface|callable
     */
    public function getRouteLoader()
    {
        return $this->routeLoader;
    }

    /**
     * @return null|RouteCompilerInterface|callable
     */
    public function getRouteCompiler()
    {
        return $this->routeCompiler;
    }


    /**
     * @return null|ActionProcessorInterface|callable
     */
    public function getActionProcessor()
    {
        return $this->actionProcessor;
    }


    /**
     * @return null|RouteCollection|callable
     */
    public function getRouteCollection()
    {
        return $this->routeCollection;
    }


    /**
     * @return null|MiddlewareCollection|callable
     */
    public function getMiddlewareCollection()
    {
        return $this->middlewareCollection;
    }

    /**
     * @return null|PatternCollection|callable
     */
    public function getPatternCollection()
    {
        return $this->patternCollection;
    }


    /**
     * @param null|ContainerInterface|callable $container
     *
     * @return static
     */
    public function setContainer($container)
    {
        $container = null
            ?? ( is_a($container, ContainerInterface::class, true) ? $container : null )
            ?? ( is_callable($container) ? $container : null )
            ?? null;

        $this->container = $container;

        return $this;
    }

    /**
     * @param null|CacheInterface|callable $cache
     *
     * @return static
     */
    public function setCache($cache)
    {
        $cache = null
            ?? ( is_a($cache, CacheInterface::class, true) ? $cache : null )
            ?? ( is_callable($cache) ? $cache : null )
            ?? null;

        $this->cache = $cache;

        return $this;
    }


    /**
     * @param null|RouteLoaderInterface|callable $routeLoader
     *
     * @return static
     */
    public function setRouteLoader($routeLoader)
    {
        $routeLoader = null
            ?? ( is_a($routeLoader, RouteLoaderInterface::class, true) ? $routeLoader : null )
            ?? ( is_callable($routeLoader) ? $routeLoader : null )
            ?? null;

        $this->routeLoader = $routeLoader;

        return $this;
    }

    /**
     * @param null|RouteCompilerInterface|callable $routeCompiler
     *
     * @return static
     */
    public function setRouteCompiler($routeCompiler)
    {
        $routeCompiler = null
            ?? ( is_a($routeCompiler, RouteCompilerInterface::class, true) ? $routeCompiler : null )
            ?? ( is_callable($routeCompiler) ? $routeCompiler : null )
            ?? null;

        $this->routeCompiler = $routeCompiler;

        return $this;
    }


    /**
     * @param null|ActionProcessorInterface|callable $actionProcessor
     *
     * @return static
     */
    public function setActionProcessor($actionProcessor)
    {
        $actionProcessor = null
            ?? ( is_a($actionProcessor, ActionProcessorInterface::class, true) ? $actionProcessor : null )
            ?? ( is_callable($actionProcessor) ? $actionProcessor : null )
            ?? null;

        $this->actionProcessor = $actionProcessor;

        return $this;
    }


    /**
     * @param null|RouteCollection|callable $routeCollection
     *
     * @return static
     */
    public function setRouteCollection($routeCollection)
    {
        $routeCollection = null
            ?? ( is_a($routeCollection, RouteCollection::class, true) ? $routeCollection : null )
            ?? ( is_callable($routeCollection) ? $routeCollection : null )
            ?? null;

        $this->routeCollection = $routeCollection;

        return $this;
    }


    /**
     * @param null|MiddlewareCollection|callable $middlewareCollection
     *
     * @return static
     */
    public function setMiddlewareCollection($middlewareCollection)
    {
        $middlewareCollection = null
            ?? ( is_a($middlewareCollection, MiddlewareCollection::class, true) ? $middlewareCollection : null )
            ?? ( is_callable($middlewareCollection) ? $middlewareCollection : null )
            ?? null;

        $this->middlewareCollection = $middlewareCollection;

        return $this;
    }

    /**
     * @param null|PatternCollection|callable $patternCollection
     *
     * @return static
     */
    public function setPatternCollection($patternCollection)
    {
        $patternCollection = null
            ?? ( is_a($patternCollection, PatternCollection::class, true) ? $patternCollection : null )
            ?? ( is_callable($patternCollection) ? $patternCollection : null )
            ?? null;

        $this->patternCollection = $patternCollection;

        return $this;
    }
}
