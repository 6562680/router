<?php

namespace Gzhegow\Router\Service\RouteBuilder;

use Gzhegow\Router\Domain\Route\CompiledRouteCollection;
use Gzhegow\Router\Service\RouteLoader\RouteLoaderInterface;
use Gzhegow\Router\Service\RouteCompiler\RouteCompilerInterface;


/**
 * RouteBuilder
 */
interface RouteBuilderInterface2
{
    /**
     * @param RouteLoaderInterface $routeLoader
     *
     * @return static
     */
    public function load(RouteLoaderInterface $routeLoader);

    /**
     * @param RouteCompilerInterface $routeCompiler
     *
     * @return CompiledRouteCollection
     */
    public function build(RouteCompilerInterface $routeCompiler) : CompiledRouteCollection;
}
