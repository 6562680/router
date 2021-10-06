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
    protected $routerContainer;


    /**
     * Constructor
     *
     * @param RouterContainerInterface $routerContainer
     */
    public function __construct(RouterContainerInterface $routerContainer)
    {
        $this->routerContainer = $routerContainer;
    }


    /**
     * @param mixed                $source
     * @param null|RouteCollection $collection
     *
     * @return RouteCollection
     */
    public function loadSource($source, RouteCollection $collection = null) : RouteCollection
    {
        $blueprintManager = $this->assertBlueprintManager($source);

        $collection = $collection ?? new RouteCollection();

        $blueprintManager->load($this->routerContainer->getRouteLoader());

        if ($routes = $blueprintManager->flushRoutes()) {
            $routeCompiler = $this->routerContainer->getRouteCompiler();

            foreach ( $routes as $route ) {
                if ($routeCompiler->supportsRoute($route)) {
                    $routeCompiler->compileRoute($route);
                }

                $compiledRoute = $route->build();

                $collection->addRoute($compiledRoute);
            }
        }

        return $collection;
    }


    /**
     * @param BlueprintManager $blueprintManager
     *
     * @return BlueprintManager
     */
    protected function assertBlueprintManager($blueprintManager) : BlueprintManager
    {
        return $blueprintManager;
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
