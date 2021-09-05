<?php


namespace Gzhegow\Router\Domain\Loader;

use Gzhegow\Router\Utils;
use Gzhegow\Router\Exceptions\RuntimeException;
use Gzhegow\Router\Domain\Route\BlueprintRoute;
use Gzhegow\Router\Domain\Route\BlueprintRouteGroup;


/**
 * ControllerRouteLoader
 */
class ControllerRouteLoader implements RouteLoaderInterface
{
    /**
     * @param BlueprintRouteGroup $group
     * @param string              $source
     *
     * @return void
     */
    public function loadSource(BlueprintRouteGroup $group, $source) : void
    {
        $classes = Utils::getClassesFromFile($source);

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
                $routes = $class::{'controller'}();

                $tpl = [
                    'method'      => 'CLI',
                    'endpoint'    => null,
                    'action'      => null,
                    'name'        => null,
                    'bindings'    => [],
                    'tags'        => [],
                    'middlewares' => [],
                ];
                foreach ( $routes as $key => $item ) {
                    $item = is_array($item)
                        ? $item
                        : [ $item ];

                    $item[ 'action' ] = $item[ 'action' ]
                        ?? ( $reflectionClass->hasMethod($key) ? $class . '@' . $key : null );

                    $item[ 'endpoint' ] = $item[ 'endpoint' ] ?? $item[ 0 ] ?? null;
                    $item[ 'method' ] = $item[ 'method' ] ?? $item[ 1 ] ?? null;

                    // @todo
                    $route = $item + $tpl;
                    $group->addBlueprint(new BlueprintRoute(/**???**/));
                }
            }
        }
    }


    /**
     * @param string $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        return (bool) Utils::getClassesFromFile($source);
    }
}
