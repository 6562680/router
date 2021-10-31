<?php

namespace Gzhegow\Router\Service\RouteCompiler\Logic;

use Gzhegow\Router\Service\RouteCompiler\RouteCompilerInterface;


/**
 * PipeRouteCompiler
 */
class PipeRouteCompiler implements RouteCompilerInterface
{
    /**
     * @var RouteCompilerInterface[]
     */
    protected $routeCompilers = [];


    /**
     * Constructor
     *
     * @param RouteCompilerInterface|RouteCompilerInterface[] $routeCompilers
     */
    public function __construct($routeCompilers = [])
    {
        $routeCompilers = is_iterable($routeCompilers)
            ? $routeCompilers
            : [ $routeCompilers ];

        foreach ( $routeCompilers as $routeCompiler ) {
            $this->addRouteCompiler($routeCompiler);
        }
    }


    /**
     * @param RouteCompilerInterface $routeCompiler
     *
     * @return static
     */
    public function addRouteCompiler(RouteCompilerInterface $routeCompiler)
    {
        $this->routeCompilers[] = $routeCompiler;

        return $this;
    }


    /**
     * @param mixed $route
     *
     * @return void
     */
    public function compileRoute($route) : void
    {
        foreach ( $this->routeCompilers as $child ) {
            if ($child->supportsRoute($route)) {
                $child->compileRoute($route);
            }
        }
    }


    /**
     * @param mixed $route
     *
     * @return bool
     */
    public function supportsRoute($route) : bool
    {
        foreach ( $this->routeCompilers as $child ) {
            if ($child->supportsRoute($route)) {
                return true;
            }
        }

        return false;
    }
}
