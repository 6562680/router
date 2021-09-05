<?php


namespace Gzhegow\Router\Domain\Loader;

use Gzhegow\Router\Domain\Route\BlueprintRouteGroup;


/**
 * ChainRouteLoader
 */
class ChainRouteLoader implements RouteLoaderInterface
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
    public function __construct($routeLoaders)
    {
        $routeLoaders = is_array($routeLoaders)
            ? $routeLoaders
            : [ $routeLoaders ];

        foreach ( $routeLoaders as $routeLoader ) {
            $this->addRouteLoader($routeLoader);
        }
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
            if ($child->supportsSource($child)) {
                return true;
            }
        }

        return false;
    }


    /**
     * @param BlueprintRouteGroup $group
     * @param mixed               $source
     *
     * @return void
     */
    public function loadSource(BlueprintRouteGroup $group, $source) : void
    {
        foreach ( $this->routeLoaders as $child ) {
            if ($child->supportsSource($child)) {
                $child->loadSource($group, $source);

                break;
            }
        }
    }
}
