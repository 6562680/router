<?php


namespace Gzhegow\Router\Service\RouteLoader;

use Gzhegow\Router\RouterContainerInterface;
use Gzhegow\Router\Domain\Route\RouteCollection;
use Gzhegow\Router\Domain\Blueprint\BlueprintManager;


/**
 * BlueprintRouteLoader
 */
class BlueprintRouteLoader implements RouteLoaderInterface
{
    /**
     * @var RouterContainerInterface
     */
    protected $routeContainer;


    /**
     * Constructor
     *
     * @param RouterContainerInterface $routeContainer
     */
    public function __construct(RouterContainerInterface $routeContainer)
    {
        $this->routeContainer = $routeContainer;
    }


    /**
     * @param BlueprintManager $source
     * @param null|object      $newthis
     *
     * @return RouteCollection
     */
    public function loadSource($source, object $newthis = null) : RouteCollection
    {
        $routeLoader = $this->routeContainer->getRouteLoader();

        $collection = new RouteCollection();

        $source->load($routeLoader, $newthis);

        if ($blueprints = $source->flushBlueprints()) {
            foreach ( $blueprints as $blueprint ) {
                $route = $blueprint->build();

                ( $corsBlueprint = $blueprint->getCors() )
                    ? $collection->addRouteWithCors($corsBlueprint->build(), $route)
                    : $collection->addRoute($route);
            }
        }

        return $collection;
    }


    /**
     * @param BlueprintManager $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        return $source instanceof BlueprintManager;
    }
}
