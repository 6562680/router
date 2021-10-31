<?php


namespace Gzhegow\Router\Service\RouteLoader;

use Gzhegow\Router\Domain\Route\RouteCollection;


/**
 * RouteLoaderInterface
 */
interface RouteLoaderInterface
{
    /**
     * @param mixed $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool;


    /**
     * @param mixed       $source
     * @param null|object $newthis
     *
     * @return RouteCollection
     */
    public function loadSource($source, object $newthis = null) : RouteCollection;
}
