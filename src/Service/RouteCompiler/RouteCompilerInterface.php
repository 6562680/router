<?php


namespace Gzhegow\Router\Service\RouteCompiler;


/**
 * RouteCompilerInterface
 */
interface RouteCompilerInterface
{
    /**
     * @param mixed $route
     *
     * @return void
     */
    public function compileRoute($route) : void;

    /**
     * @param mixed $route
     *
     * @return bool
     */
    public function supportsRoute($route) : bool;
}
