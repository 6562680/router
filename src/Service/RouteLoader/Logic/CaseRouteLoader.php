<?php


namespace Gzhegow\Router\Service\RouteLoader\Logic;

use Gzhegow\Router\Domain\Route\RouteCollection;
use Gzhegow\Router\Service\RouteLoader\RouteLoaderInterface;


/**
 * CaseRouteLoader
 */
class CaseRouteLoader implements RouteLoaderInterface
{
    /**
     * @var RouteLoaderInterface[]
     */
    protected $routeLoaders = [];


    /**
     * Constructor
     *
     * @param RouteLoaderInterface|RouteLoaderInterface[] $routeLoaders
     */
    public function __construct($routeLoaders = [])
    {
        $routeLoaders = is_iterable($routeLoaders)
            ? $routeLoaders
            : [ $routeLoaders ];

        foreach ( $routeLoaders as $routeLoader ) {
            $this->addRouteLoader($routeLoader);
        }
    }


    /**
     * @param mixed       $source
     * @param null|object $newthis
     *
     * @return RouteCollection
     */
    public function loadSource($source, object $newthis = null) : RouteCollection
    {
        $collection = new RouteCollection();

        foreach ( $this->routeLoaders as $child ) {
            if ($child->supportsSource($source)) {
                $childCollection = $child->loadSource($source, $newthis);

                $collection->merge($childCollection);
                break;
            }
        }

        return $collection;
    }


    /**
     * @param RouteLoaderInterface $routeLoader
     *
     * @return static
     */
    public function addRouteLoader(RouteLoaderInterface $routeLoader)
    {
        $this->routeLoaders[] = $routeLoader;

        return $this;
    }


    /**
     * @param mixed $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        foreach ( $this->routeLoaders as $child ) {
            if ($child->supportsSource($source)) {
                return true;
            }
        }

        return false;
    }
}
