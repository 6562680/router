<?php


namespace Gzhegow\Router\Service\RouteLoader;

use Gzhegow\Router\Vendor\Helper;
use Gzhegow\Router\Domain\Route\RouteCollection;


/**
 * DirectoryRouteLoader
 */
class DirectoryRouteLoader implements RouteLoaderInterface
{
    /**
     * @var RouteLoaderInterface
     */
    protected $routeLoader;


    /**
     * Constructor
     *
     * @param RouteLoaderInterface $routeLoader
     */
    public function __construct(RouteLoaderInterface $routeLoader)
    {
        $this->routeLoader = $routeLoader;
    }


    /**
     * @param string|\SplFileInfo $source
     * @param null|object         $newthis
     *
     * @return RouteCollection
     */
    public function loadSource($source, object $newthis = null) : RouteCollection
    {
        $collection = $collection ?? new RouteCollection();

        $it = new \RecursiveDirectoryIterator($source);
        $iit = new \RecursiveIteratorIterator($it);

        foreach ( $iit as $spl ) {
            if ($this->routeLoader->supportsSource($spl)) {
                $childCollection = $this->routeLoader->loadSource($spl, $newthis);

                $collection->addRoutes($childCollection->getRoutes());
            }
        }

        return $collection;
    }


    /**
     * @param string|\SplFileInfo $source
     *
     * @return bool
     */
    public function supportsSource($source) : bool
    {
        return (bool) Helper::filterDirectory($source);
    }
}
