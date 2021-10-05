<?php


namespace Gzhegow\Router\Service\RouteLoader;

use Gzhegow\Router\Vendor\Helper;
use Gzhegow\Router\Domain\Blueprint\Blueprint;
use Gzhegow\Router\Exceptions\RuntimeException;
use Gzhegow\Router\Domain\Route\RouteCollection;
use Gzhegow\Router\Service\RouteCompiler\RouteCompilerInterface;


/**
 * FileControllerRouteLoader
 */
class FileControllerRouteLoader implements RouteLoaderInterface
{
    /**
     * @var RouteCompilerInterface
     */
    protected $routeCompiler;


    /**
     * Constructor
     *
     * @param RouteCompilerInterface $routeCompiler
     */
    public function __construct(RouteCompilerInterface $routeCompiler)
    {
        $this->routeCompiler = $routeCompiler;
    }


    /**
     * @param mixed                $source
     * @param null|RouteCollection $collection
     *
     * @return RouteCollection
     */
    public function loadSource($source, RouteCollection $collection = null) : RouteCollection
    {
        $collection = $collection ?? new RouteCollection();

        $classes = Helper::extractClassesFromFile($source);

        foreach ( $classes as $class ) {
            try {
                $reflectionClass = new \ReflectionClass($class);
            }
            catch ( \ReflectionException $e ) {
                throw new RuntimeException($e->getMessage(), null, $e);
            }

            if ($reflectionClass->hasMethod('controller')
                && $reflectionClass->getMethod('controller')->isStatic()
            ) {
                $routes = $class::{'controller'}($collection);

                foreach ( $routes as $classMethod => $routeArray ) {
                    $routeArray = null
                        ?? ( is_array($routeArray) ? $routeArray : null )
                        ?? ( is_string($routeArray) ? [ 'GET', $routeArray ] : null )
                        ?? null;

                    if (null !== $routeArray) {
                        $routeArray[ 'method' ] = $routeArray[ 'method' ] ?? $routeArray[ 0 ] ?? null;
                        $routeArray[ 'endpoint' ] = $routeArray[ 'endpoint' ] ?? $routeArray[ 1 ] ?? null;
                        $routeArray[ 'action' ] = $routeArray[ 'action' ]
                            ?? ( $reflectionClass->hasMethod($classMethod)
                                ? $class . '@' . $classMethod
                                : null
                            );

                        $route = ( new Blueprint() )
                            ->method($routeArray[ 'method' ])
                            ->endpoint($routeArray[ 'endpoint' ])
                            ->action($routeArray[ 'action' ]);

                        $this->routeCompiler->compileRoute($route);

                        $compiledRoute = $route->build();

                        $collection->addRoute($compiledRoute);
                    }
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
