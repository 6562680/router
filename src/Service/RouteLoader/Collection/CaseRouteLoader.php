<?php


namespace Gzhegow\Router\Service\RouteLoader\Collection;

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
     * @param mixed                $source
     * @param null|RouteCollection $collection
     *
     * @return RouteCollection
     */
    public function loadSource($source, RouteCollection $collection = null) : RouteCollection
    {
        $routeCollection = new RouteCollection();

        foreach ( $this->routeLoaders as $child ) {
            if ($child->supportsSource($source)) {
                $collection = $child->loadSource($source, $collection);

                foreach ( $collection->getRoutes() as $route ) {
                    $routeCollection->addRoute($route);
                }
            }
        }

        return $routeCollection;
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
