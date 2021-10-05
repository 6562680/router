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
    protected $fileRouteLoader;


    /**
     * Constructor
     *
     * @param RouteLoaderInterface $fileRouteLoader
     */
    public function __construct(RouteLoaderInterface $fileRouteLoader)
    {
        $this->fileRouteLoader = $fileRouteLoader;
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

        $it = new \RecursiveDirectoryIterator($source);
        $iit = new \RecursiveIteratorIterator($it);

        foreach ( $iit as $spl ) {
            if ($this->fileRouteLoader->supportsSource($spl)) {
                $childCollection = $this->fileRouteLoader->loadSource($spl, $collection);

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
        if (null === Helper::filterDirectory($source)) {
            return false;
        }

        $it = new \RecursiveDirectoryIterator($source);
        $iit = new \RecursiveIteratorIterator($it);

        foreach ( $iit as $spl ) {
            if ($this->fileRouteLoader->supportsSource($spl)) {
                return true;
            }
        }

        return false;
    }
}
