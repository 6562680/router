<?php


namespace Gzhegow\Router\Service\RouteLoader;

use Gzhegow\Router\Vendor\Helper;
use Gzhegow\Router\RouterContainerInterface;
use Gzhegow\Router\Domain\Blueprint\Blueprint;
use Gzhegow\Router\Exceptions\RuntimeException;
use Gzhegow\Router\Domain\Route\RouteCollection;


/**
 * FileControllerStaticRouteLoader
 */
class FileControllerStaticRouteLoader implements RouteLoaderInterface
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
     * @param mixed       $source
     * @param null|object $newthis
     *
     * @return RouteCollection
     */
    public function loadSource($source, object $newthis = null) : RouteCollection
    {
        $collection = new RouteCollection();

        // function statically caches the result [ see supports() below ]
        // so there's fast call
        $classes = Helper::extractClassesFromFile($source);

        foreach ( $classes as $class ) {
            try {
                $reflectionClass = new \ReflectionClass($class);

                if ($reflectionClass->getMethod($method = 'controller')) {
                    continue;
                }

                $reflectionMethod = $reflectionClass->getMethod($method);

                if (! $reflectionMethod->isStatic()) {
                    continue;
                }
            }
            catch ( \ReflectionException $e ) {
                throw new RuntimeException($e->getMessage(), null, $e);
            }

            $routeCompiler = $this->routerContainer->getRouteCompiler();

            $routes = $class::{'controller'}($collection);

            foreach ( $routes as $classMethod => $routeArray ) {
                $routeArray = null
                    ?? ( is_array($routeArray) ? $routeArray : null )
                    ?? ( is_string($routeArray) ? [ 'GET', $routeArray ] : null )
                    ?? null;

                if (null !== $routeArray) {
                    $routeArray[ 'method' ] = $routeArray[ 'method' ] ?? $routeArray[ 0 ] ?? null;
                    $routeArray[ 'endpoint' ] = $routeArray[ 'endpoint' ] ?? $routeArray[ 1 ] ?? null;
                    $routeArray[ 'action' ] = $routeArray[ 'action' ] ?? $routeArray[ 2 ]
                        ?? ( $reflectionClass->hasMethod($classMethod)
                            ? $class . '@' . $classMethod
                            : null
                        );

                    $route = ( new Blueprint() )
                        ->method($routeArray[ 'method' ])
                        ->endpoint($routeArray[ 'endpoint' ])
                        ->action($routeArray[ 'action' ]);

                    $routeCompiler->compileRoute($route);

                    $compiledRoute = $route->build();

                    $collection->addRoute($compiledRoute);
                }
            }
        }

        return $collection;
    }


    /**
     * @param string $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        return Helper::filterFilePhp($source)
            && Helper::extractClassesFromFile($source);
    }
}
