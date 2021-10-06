<?php


namespace Gzhegow\Router\Domain\Configuration;

use Psr\SimpleCache\CacheInterface;
use Psr\Container\ContainerInterface;
use Gzhegow\Router\RouterFactoryInterface;
use Gzhegow\Router\Service\RouteLoader\RouteLoaderInterface;
use Gzhegow\Router\Service\RouteCompiler\RouteCompilerInterface;
use Gzhegow\Router\Service\ActionProcessor\ActionProcessorInterface;


/**
 * Configuration
 */
class Configuration
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var RouterFactoryInterface
     */
    protected $routerFactory;

    /**
     * @var RouteLoaderInterface
     */
    protected $routeLoader;
    /**
     * @var RouteCompilerInterface
     */
    protected $routeCompiler;

    /**
     * @var ActionProcessorInterface
     */
    protected $actionProcessor;

    /**
     * @var mixed
     */
    protected $corsMiddleware;


    /**
     * @return null|ContainerInterface
     */
    public function getContainer() : ?ContainerInterface
    {
        return $this->container;
    }


    /**
     * @return null|CacheInterface
     */
    public function getCache() : ?CacheInterface
    {
        return $this->cache;
    }


    /**
     * @return null|RouterFactoryInterface
     */
    public function getRouterFactory() : ?RouterFactoryInterface
    {
        return $this->routerFactory;
    }


    /**
     * @return null|RouteLoaderInterface
     */
    public function getRouteLoader() : ?RouteLoaderInterface
    {
        return $this->routeLoader;
    }

    /**
     * @return null|RouteCompilerInterface
     */
    public function getRouteCompiler() : ?RouteCompilerInterface
    {
        return $this->routeCompiler;
    }


    /**
     * @return null|ActionProcessorInterface
     */
    public function getActionProcessor() : ?ActionProcessorInterface
    {
        return $this->actionProcessor;
    }


    /**
     * @return mixed
     */
    public function getCorsMiddleware()
    {
        return $this->corsMiddleware;
    }


    /**
     * @param null|ContainerInterface $container
     *
     * @return static
     */
    public function setContainer(?ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }


    /**
     * @param null|CacheInterface $cache
     *
     * @return static
     */
    public function setCache(?CacheInterface $cache)
    {
        $this->cache = $cache;

        return $this;
    }


    /**
     * @param null|RouterFactoryInterface $routerFactory
     *
     * @return static
     */
    public function setRouterFactory(?RouterFactoryInterface $routerFactory)
    {
        $this->routerFactory = $routerFactory;

        return $this;
    }


    /**
     * @param null|RouteLoaderInterface $routeLoader
     *
     * @return static
     */
    public function setRouteLoader(?RouteLoaderInterface $routeLoader)
    {
        $this->routeLoader = $routeLoader;

        return $this;
    }

    /**
     * @param null|RouteCompilerInterface $routeCompiler
     *
     * @return static
     */
    public function setRouteCompiler(?RouteCompilerInterface $routeCompiler)
    {
        $this->routeCompiler = $routeCompiler;

        return $this;
    }


    /**
     * @param null|ActionProcessorInterface $actionProcessor
     *
     * @return static
     */
    public function setActionProcessor(?ActionProcessorInterface $actionProcessor)
    {
        $this->actionProcessor = $actionProcessor;

        return $this;
    }


    /**
     * @param null|mixed $corsMiddleware
     *
     * @return static
     */
    public function setCorsMiddleware($corsMiddleware)
    {
        $this->corsMiddleware = $corsMiddleware;

        return $this;
    }
}
