<?php


namespace Gzhegow\Router\Service\RouteLoader;

use Gzhegow\Router\RouterContainerInterface;
use Gzhegow\Router\Domain\Route\RouteCollection;
use Gzhegow\Router\Domain\Blueprint\BlueprintManager;
use Gzhegow\Router\Exceptions\Runtime\UnexpectedValueException;


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
        if (null === ( $blueprintManager = $this->filterBlueprintManager($source) )) {
            throw new UnexpectedValueException(
                [ 'Invalid source: %s', $source ]
            );
        }

        $collection = $collection ?? new RouteCollection();

        $routeLoader = $this->routerContainer->getRouteLoader();

        $blueprintManager->load($routeLoader);

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
     * @param BlueprintManager $source
     *
     * @return null|BlueprintManager
     */
    protected function filterBlueprintManager($source) : ?BlueprintManager
    {
        return $source instanceof BlueprintManager
            ? $source
            : null;
    }


    /**
     * @param BlueprintManager $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        return (bool) $this->filterBlueprintManager($source);
    }
}
